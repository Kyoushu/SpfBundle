<?php

namespace Kyoushu\SpfBundle\Templating;

use Kyoushu\SpfBundle\Exception\KyoushuSpfBundleException;

class Fragment
{

    const TYPE_TITLE = 'title';
    const TYPE_URL = 'url';
    const TYPE_HEAD = 'head';
    const TYPE_ATTR = 'attr';
    const TYPE_BODY = 'body';
    const TYPE_FOOT = 'foot';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $value;

    /**
     * @param string $name
     * @param string $type
     * @param string|null $value
     * @return Fragment
     */
    public static function create($name, $type = self::TYPE_BODY, $value = null)
    {
        return new Fragment($name, $type, $value);
    }

    /**
     * @param string $name
     * @param string $type
     * @param string|null $value
     */
    public function __construct($name, $type = self::TYPE_BODY, $value = null)
    {
        $this->value = $value;
        $this->name = $name;
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
        $value = $this->getValue();
        if($value !== null) return $value;

        $template = $this->createTemplate($twig, $view);
        $block = $this->findBlock($template);

        if($block === null) return null;

        ob_start();
        call_user_func($block, $parameters);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null|string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param \Twig_Template $template
     * @return array|null
     */
    protected function findBlock(\Twig_Template $template)
    {
        foreach($template->getBlocks() as $name => $block){
            if($name === $this->getName()) return $block;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $data
     * @param \Twig_Environment $twig
     * @param string $view
     * @param array $parameters
     * @return array
     * @throws KyoushuSpfBundleException
     */
    public function mergeResponseData(array $data, \Twig_Environment $twig, $view, array $parameters = array())
    {
        $name = $this->getName();
        $type = $this->getType();
        $value = $this->render($twig, $view, $parameters);

        if($value === null) return $data;

        if(in_array($type, array(self::TYPE_TITLE, self::TYPE_URL))){
            // Set
            $data[$type] = $value;
        }
        elseif(in_array($type, array(self::TYPE_HEAD, self::TYPE_FOOT))){
            // Append
            if(!isset($data['type'])) $data[$type] = '';
            $data[$type] .= $value;
        }
        elseif(in_array($type, array(self::TYPE_BODY, self::TYPE_ATTR))){
            // Key / Value
            if(!isset($data['type'])) $data[$type] = array();
            $data[$type][$this->getName()] = $value;
        }
        else{
            throw new KyoushuSpfBundleException(sprintf(
                'Unsupported fragment type %s',
                $type
            ));
        }

        return $data;

    }

}