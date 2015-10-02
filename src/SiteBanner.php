<?php
/*
 * Site Banner class
 *
 * (c) Studio 24 <info@studio24.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Studio24;

class SiteBanner {

    /**
     * Current environment name
     *
     * @var string
     */
    protected $environment;

    /**
     * What environments to hide the staging banner
     *
     * Usually the staging banner is not shown in the live production environment so
     * we exclude it by including the production env name here
     *
     * @var array
     */
    protected $hideBannerOnEnvironments = array(
        'production'
    );

    /**
     * Constructor
     *
     * @param $environment Current environment
     */
    public function __construct($environment)
    {
        $this->environment = strip_tags($environment);
    }

    public function setHideBannerOnEnvironments(array $envNames)
    {
        $this->hideBannerOnEnvironments = $envNames;
    }

    /**
     * Return the staging banner HTML
     *
     * @param boolean $embedCss Whether to embed CSS styles within HTML, if false get CSS via SiteBanner->getCss()
     * @return string
     */
    public function getBanner($embedCss = true)
    {
        $html = '';


        // Current environment
        $environment = $this->environment;

        // Current branch
        exec('git rev-parse --abbrev-ref HEAD', $output, $return);
        $branch =  $output[0];

        //$branch = $git->getCurrentBranch();

        // Current commit
        exec('git log --abbrev-commit --no-merges -n1', $output, $return);
        $numLines = count($output);

        for ($i = 0; $i < $numLines; $i++) {
            $tmp = explode(' ', $output[$i]);

            if (count($tmp) == 2 && $tmp[0] == 'commit') {
                $sha1 = $tmp[1];
            }

            else if (count($tmp) == 9 && $tmp[0] == 'Date:') {
                $revisions[] = array(
                    'date' => \DateTime::createFromFormat(
                        'D M j H:i:s Y O',
                        join(' ', array_slice($tmp, 3))
                    ),
                    'sha1' => $sha1
                );
            }
        }

        $lastCommit = $revisions[0]['sha1'];
        $css = $this->getCss();

        // Repo link
        //https://bitbucket.org/studio24/crossrail/commits/def75f4
        // https://github.com/studio24/joindin-api/commit/7636e40

        $warningImg = $this->getWarningSymbol();

        $html = <<<EOD
<style>
$css
</style>
<div id="s24-dev-site-banner">
<div>
    <div class="c1"><span class="warning">$warningImg</span> You are viewing the $environment website</div>
    <div class="c2">Branch $branch</div>
    <div class="c3">Last commit $lastCommit<div>
</div>

EOD;

        return $html;
    }

    /**
     * Return CSS as a string so we have flexibility on where this is placed
     *
     * @return string
     */
    public function getCss()
    {
        return <<<EOD   
#s24-dev-site-banner {
    position: absolute;
    top: 0;
    left: 0;
    height: 2.5em;
    width: 100%;
    background: #FF0 no-repeat 2px 100%;
    color: #000;
    font-family: Helvetica, sans-serif;
    font-size: 1em;
}

#s24-dev-site-banner .c1,
#s24-dev-site-banner .c2
#s24-dev-site-banner .c3 {
    position: relative;
    top: 0;
    left: 0;
    padding: 0.5em 1em;
    width: 33%;
}

#s24-dev-site-banner .c2 {
    left: 30%;
}

#s24-dev-site-banner .c3 {
    left: 60%;
}

#s24-dev-site-banner svg.warning {
    height: 1rem;
}

#s24-dev-site-banner div {
    border-right: 1px solid black;
}

#s24-dev-site-banner div:last-child {
    border: none;
}

EOD;

    }

    protected function getWarningSymbol()
    {
        return <<<EOD
<svg class="warning" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8" preserveRatio="xMidYMid meet">
    <switch>
        <path d="M3.09 0c-.06 0-.1.04-.13.09l-2.94 6.81c-.02.05-.03.13-.03.19v.81c0 .05.04.09.09.09h6.81c.05 0 .09-.04.09-.09v-.81c0-.05-.01-.14-.03-.19l-2.94-6.81c-.02-.05-.07-.09-.13-.09h-.81zm-.09 3h1v2h-1v-2zm0 3h1v1h-1v-1z" />
  </switch>
</svg>
EOD;

    }

    /**
     * @param  string $command
     * @throws RuntimeException
     */
    protected function execute($command)
    {
        $cwd = getcwd();
        chdir($this->repositoryPath);
        exec($command, $output, $returnValue);
        chdir($cwd);
        if ($returnValue !== 0) {
            throw new RuntimeException(implode("\r\n", $output));
        }
        return $output;
    }

}