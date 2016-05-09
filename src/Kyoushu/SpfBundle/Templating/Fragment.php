<?php

namespace Kyoushu\SpfBundle\Templating;

use Kyoushu\SpfBundle\Exception\KyoushuSpfBundleException;

class Fragment
{

    const TYPE_HEAD = 'head';
    const TYPE_BODY = 'body';
    const TYPE_FOOT = 'foot';

    /**
     * @var string
     */
    protected $blockName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $blockName
     * @param string $type
     * @return Fragment
     */
    public static function create($blockName, $type = self::TYPE_BODY)
    {
        return new Fragment($blockName, $type);
    }

    /**
     * @param string $blockName
     * @param string $type
     */
    public function __construct($blockName, $type = self::TYPE_BODY)
    {
        $this->blockName = $blockName;
        $this->type = $type;
    }

    /**
     * @param \Twig_Environment $twig
     * @param string $view
     * @return \Twig_Template
     */
    protected function createTemplate(\Twig_Environment $twig, $view)
    {
        return $twig->loadTemplate($view);
    }

    /**
     * @param \Twig_Environment $twig
     * @param string $view
     * @param array $parameters
     * @return string
     * @throws KyoushuSpfBundleException
     */
    public function render(\Twig_Environment $twig, $view, array $parameters = array())
    {
        $template = $this->createTemplate($twig, $view);
        $block = $this->findBlock($template);

        ob_start();
        call_user_func($block, $parameters);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    /**
     * @param \Twig_Template $template
     * @return array
     * @throws KyoushuSpfBundleException
     */
    protected function findBlock(\Twig_Template $template)
    {
        foreach($template->getBlocks() as $blockName => $block){
            if($blockName === $this->getBlockName()) return $block;
        }
        throw new KyoushuSpfBundleException(sprintf(
            'Fragment could not find a block named "%s" in %s',
            $this->getBlockName(),
            $template->getTemplateName()
        ));
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return $this->blockName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}