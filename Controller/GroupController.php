<?php

namespace Oro\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\UserBundle\Entity\Group;
use Oro\Bundle\UserBundle\Datagrid\GroupDatagridManager;
use Oro\Bundle\UserBundle\Datagrid\LightUserDatagridManager;

/**
 * @Route("/group")
 */
class GroupController extends Controller
{
    /**
     * Create group form
     *
     * @Route("/create", name="oro_user_group_create")
     * @Template("OroUserBundle:Group:edit.html.twig")
     */
    public function createAction()
    {
        return $this->editAction(new Group());
    }

    /**
     * Edit group form
     *
     * @Route("/edit/{id}/{_format}",
     * name="oro_user_group_edit",
     * requirements={"id"="\d+", "_format"="html|json"},
     * defaults={"id"=0, "_format"="html"})
     */
    public function editAction(Group $entity)
    {
        $backUrl = $this->getRedirectUrl($this->generateUrl('oro_user_group_index'));
        if ($this->get('oro_user.form.handler.group')->process($entity)) {
            $this->get('session')->getFlashBag()->add('success', 'Group successfully saved');

            if (!$this->getRequest()->get('_widgetContainer')) {
                return $this->redirect($backUrl);
            }
        }

        $this->get('oro_user.group_user_datagrid_manager.default_query_factory')
             ->setQueryBuilder(
                 $this->get('oro_user.group_manager')->getUserQueryBuilder($entity)
             );

        /** @var $userGridManager LightUserDatagridManager */
        $userGridManager = $this->get('oro_user.group_user_datagrid_manager');
        $userGridManager->getRouteGenerator()->setRouteParameters(array('id' => $entity->getId()));

        if ('json' == $this->getRequest()->getRequestFormat()) {
            $view = 'OroGridBundle:Datagrid:list.json.php';
        } else {
            $view = 'OroUserBundle:Group:edit.html.twig';
        }

        return $this->render(
            $view,
            array(
                'datagrid' => $userGridManager->getDatagrid(),
                'form' => $this->get('oro_user.form.group')->createView(),
            )
        );
    }

    /**
     * @Route("/remove/{id}", name="oro_user_group_remove", requirements={"id"="\d+"})
     */
    public function removeAction(Group $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Group successfully removed');

        return $this->redirect($this->generateUrl('oro_user_group_index'));
    }

    /**
     * @Route("/{page}/{limit}", name="oro_user_group_index", requirements={"page"="\d+","limit"="\d+"}, defaults={"page"=1,"limit"=20})
     * @Template
     */
    /*public function indexAction($page, $limit)
    {
        $query = $this
            ->getDoctrine()
            ->getEntityManager()
            ->createQuery('SELECT g FROM OroUserBundle:Group g ORDER BY g.id');

        return array(
            'pager'  => $this->get('knp_paginator')->paginate($query, $page, $limit),
        );
    }*/

    /**
     * @Route(
     *      "/{_format}",
     *      name="oro_user_group_index",
     *      requirements={"_format"="html|json"},
     *      defaults={"_format" = "html"}
     * )
     */
    public function indexAction(Request $request)
    {
        /** @var $groupGridManager GroupDatagridManager */
        $groupGridManager = $this->get('oro_user.group_datagrid_manager');
        $datagrid = $groupGridManager->getDatagrid();

        if ('json' == $request->getRequestFormat()) {
            $view = 'OroGridBundle:Datagrid:list.json.php';
        } else {
            $view = 'OroUserBundle:Group:index.html.twig';
        }

        return $this->render(
            $view,
            array(
                'datagrid' => $datagrid,
                'form'     => $datagrid->getForm()->createView()
            )
        );
    }

    /**
     * Get redirect URLs
     *
     * @param  string $default
     * @return string
     */
    protected function getRedirectUrl($default)
    {
        $flashBag = $this->get('session')->getFlashBag();
        if ($this->getRequest()->query->has('back')) {
            $backUrl = $this->getRequest()->get('back');
            $flashBag->set('backUrl', $backUrl);
        } elseif ($flashBag->has('backUrl')) {
            $backUrl = $flashBag->get('backUrl');
            $backUrl = reset($backUrl);
        } else {
            $backUrl = null;
        }

        return $backUrl ? $backUrl : $default;
    }
}
