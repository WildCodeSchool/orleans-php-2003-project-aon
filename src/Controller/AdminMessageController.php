<?php

namespace App\Controller;

use App\Model\MessageManager;

class AdminMessageController extends AbstractController
{

    public function deleteMessage(int $id): void
    {
        $messageManager = new MessageManager();
        $messageManager->delete($id);
        header('Location:/admin/index');
    }
}
