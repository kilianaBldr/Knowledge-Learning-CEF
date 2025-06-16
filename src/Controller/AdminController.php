<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AdminController
 * 
 * Controller dedicated to handling admin dashboard and formation management views.
 * Access to these routes should be restricted to users with ROLE_ADMIN.
 */
final class AdminController extends AbstractController
{
    /**
     * Displays the main admin dashboard.
     *
     * Route: /admin
     * Name: admin_dashboard
     * 
     * @return Response The rendered admin dashboard page
     */
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig');
    }

    /**
     * Displays the admin dashboard for managing formations.
     *
     * Route: /admin/formations
     * Name: admin_formation_dashboard
     * 
     * @return Response The rendered formation management page
     */
    #[Route('/admin/formations', name: 'admin_formation_dashboard')]
    public function formations(): Response
    {
        return $this->render('adminFormations/formation.html.twig');
    }
}