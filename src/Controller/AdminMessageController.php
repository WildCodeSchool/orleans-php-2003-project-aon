<?php

namespace App\Controller;

use App\Model\MessageManager;

class AdminMessageController extends AbstractController
{

    public function deleteMessage(): void
    {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $messageManager = new MessageManager();
            $messageManager->delete($id);
            header('Location:/admin/index');
        }
    }
}
