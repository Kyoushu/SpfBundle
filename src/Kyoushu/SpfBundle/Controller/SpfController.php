<?php

namespace Kyoushu\SpfBundle\Controller;

use Kyoushu\SpfBundle\Templating\Fragment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class SpfController extends Controller
{

    const SPF_QUERY_NAVIGATE = 'navigate';
    const SPF_QUERY_NAVIGATE_BACK = 'navigate-back';
    const SPF_QUERY_NAVIGATE_FORWARD = 'navigate-forward';

    /**
     * @return null|Request
     */
    protected function getRequest()
    {
        return $this->get('request_stack')->getCurrentRequest();
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isNavigateRequest(Request $request)
    {
        if(!$request->query->has('spf')) return false;
        $value = $request->query->get('spf');
        return in_array($value, array(self::SPF_QUERY_NAVIGATE, self::SPF_QUERY_NAVIGATE_BACK, self::SPF_QUERY_NAVIGATE_FORWARD));
    }

    /**
     * @return Fragment[]
     */
    private function getDefaultFragments()
    {
        return $this->get('kyoushu_spf.default_fragment_registry')->all();
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param Fragment[] $fragments
     * @param Response|null $response
     * @param Request|null $request
     * @return Response
     */
    protected function renderSpf($view, array $parameters = array(), $fragments = array(), Request $request = null, Response $response = null)
    {
        if($request === null) $request = $this->getRequest();

        $navigate = $this->isNavigateRequest($request);

        if($navigate){

            if($response === null) $response = new Response();

            $data = array();
            $twig = $this->get('twig');
            
            foreach($this->getDefaultFragments() as $fragment){
                $data = $fragment->mergeResponseData($data, $twig, $view, $parameters);
            }

            foreach($fragments as $fragment){
                $data = $fragment->mergeResponseData($data, $twig, $view, $parameters);
            }

            $response->setContent(json_encode($data, JSON_PRETTY_PRINT));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        else{
            return parent::render($view, $parameters, $response);
        }

    }


}