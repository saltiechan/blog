
<?php

use yii\db\Migration;

class m261106_120000_add_post_permissions extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $roleAdmin = $auth->getRole('Admin');
        $roleAuthor = $auth->getRole('Author');

        if (!$roleAdmin || !$roleAuthor) {
            throw new Exception('Role Admin atau Author tidak ditemukan.');
        }

        $permissions = [
            'post/viewAll' => 'Melihat seluruh artikel',
            'post/viewOwn' => 'Melihat artikel sendiri',
            'post/create' => 'Membuat artikel',
            'post/updateOwn' => 'Mengedit artikel sendiri',
            'post/submitReview' => 'Mengajukan artikel untuk review',
            'post/approve' => 'Menyetujui artikel',
            'post/reject' => 'Menolak artikel',
        ];

        foreach ($permissions as $name => $description) {
            $permission = $auth->getPermission($name);

            if (!$permission) {
                $permission = $auth->createPermission($name);
                $permission->description = $description;
                $auth->add($permission);
            }
        }

        $adminPermissions = [
            'post/viewAll',
            'post/approve',
            'post/reject',
        ];

        foreach ($adminPermissions as $name) {
            $permission = $auth->getPermission($name);

            if (!$auth->hasChild($roleAdmin, $permission)) {
                $auth->addChild($roleAdmin, $permission);
            }
        }

        $authorPermissions = [
            'post/viewOwn',
            'post/create',
            'post/updateOwn',
            'post/submitReview',
        ];

        foreach ($authorPermissions as $name) {
            $permission = $auth->getPermission($name);

            if (!$auth->hasChild($roleAuthor, $permission)) {
                $auth->addChild($roleAuthor, $permission);
            }
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $permissions = [
            'post/viewAll',
            'post/viewOwn',
            'post/create',
            'post/updateOwn',
            'post/submitReview',
            'post/approve',
            'post/reject',
        ];

        foreach ($permissions as $name) {
            $permission = $auth->getPermission($name);

            if ($permission) {
                $auth->remove($permission);
            }
        }
    }
}
