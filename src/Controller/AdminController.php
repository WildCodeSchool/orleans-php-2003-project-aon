<?php

namespace App\Controller;

use App\Model\EventManager;

/**
 * Class AdminController
 *
 */
class AdminController extends AbstractController
{
    /**
     * Handle event deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $eventManager = new EventManager();
        $eventManager->delete($id);
        header('Location:/Admin/index');
    }
}
