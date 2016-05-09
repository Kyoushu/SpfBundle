<?php

namespace Kyoushu\SpfBundle\Controller;

use Kyoushu\SpfBundle\Templating\Fragment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class SpfController extends Controller
{

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
    protected function isNavigateRequest(Request $request)
    {
        if(!$request->query->has('spf')) return false;
        return $request->query->get('spf') === 'navigate';
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

            $data = array(
                'head' => '',
                'body' => array(),
                'foot' => ''
            );

            $twig = $this->get('twig');

            foreach($fragments as $fragment){
                $type = $fragment->getType();
                $html = $fragment->render($twig, $view, $parameters);

                if($fragment->getType() === Fragment::TYPE_BODY){
                    $data['body'][$fragment->getBlockName()] = $html;
                }
                else{
                    if(!isset($data[$type])) $data[$type] = '';
                    $data[$type] .= $html;
                }
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