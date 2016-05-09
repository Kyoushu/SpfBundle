<?php

namespace Kyoushu\SpfBundle\Templating;

class FragmentRegistry
{

    /**
     * @var Fragment[]
     */
    protected $fragments;

    public function __construct()
    {
        $this->fragments = array();
    }

    /**
     * @param Fragment $fragment
     * @return bool
     */
    public function has(Fragment $fragment)
    {
        return in_array($fragment, $this->fragments, true);
    }

    /**
     * @param Fragment $fragment
     * @return $this
     */
    public function add(Fragment $fragment)
    {
        if($this->has($fragment)) return $this;
        $this->fragments[] = $fragment;
        return $this;
    }

    /**
     * @param Fragment $fragment
     */
    public function remove(Fragment $fragment)
    {
        $key = array_search($fragment, $this->fragments, true);
        if($key === null) return;
        unset($this->fragments[$key]);
    }

    /**
     * @return Fragment[]
     */
    public function all()
    {
        return $this->fragments;
    }

}