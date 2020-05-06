<?php


namespace App\Controller;

use App\Model\WhoAreUsManager;

class AdminWhoAreUsController extends AbstractController
{

    /**
     * Display item edition page specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(int $id): string
    {
        $whoAreUsManager = new WhoAreUsManager();
        $whoAreUs = $whoAreUsManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $whoAreUs['description'] = $_POST['description'];
            $whoAreUs['picture'] = $_POST['picture'];

            $whoAreUsManager->update($whoAreUs);
        }

        return $this->twig->render('WhoAreUs/edit.html.twig', ['whoAreUs' => $whoAreUs]);
    }
}
