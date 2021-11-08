<?php
declare(strict_types=1);

include_once "../autoload.php";

$auth = new SecureUserAuthentication();
if($auth->isUserConnected()){
    $user = $auth->getUser();
<<<<<<< HEAD
    if(isset($_POST['password'], $_POST['newMail'], $_POST['newMailRepeat'])
        && !empty($_POST['password']) && !empty($_POST['newMail']) && !empty($_POST['newMailRepeat'])){
        $oldMail = $user->getEmail();
        $password = WebPage::escapeString($_POST['password']);
        $newMail = WebPage::escapeString($_POST['newMail']);
        $newMailRepeat = WebPage::escapeString($_POST['newMailRepeat']);

        $req = MyPDO::getInstance()->prepare(<<<SQL
            SELECT * FROM Users
            WHERE email = :email AND password = :password
        SQL);
        $req->execute(['email' => $oldMail, 'password' => $password]);
        if($req->rowCount() > 0){
            if($newMail === $newMailRepeat){
                $checkMailReq = MyPDO::getInstance()->prepare(<<<SQL
                    SELECT email FROM Users
                    WHERE email = :email;
                SQL);
                $checkMailReq->execute(['email' => $newMail]);
                if($checkMailReq->rowCount() < 1) {
                    $req2 = MyPDO::getInstance()->prepare(<<<SQL
                        UPDATE Users
                        SET email = :email 
                        WHERE userId = :userId;
                    SQL);
                    $req2->execute(['userId' => $user->getUserId(), 'email' => $newMail]);
                    $user->flush();
                    header('Location: ../profile.php');
                } else header('Location: ../profile_change_mail.php?err_email_already');
            } else header('Location: ../profile_change_mail.php?err_repeatMail');
        } else header('Location: ../profile_change_mail.php?err_password');
=======

    if(isset($_POST['oldMail'], $_POST['newMail'], $_POST['repeatNewMail'])
        && !empty($_POST['oldMail']) && !empty($_POST['newMail']) && !empty($_POST['repeatNewMail'])){
        $oldMail = WebPage::escapeString($_POST['oldMail']);
        $newMail = WebPage::escapeString($_POST['newMail']);
        $repeatNewMail = WebPage::escapeString($_POST['repeatNewMail']);

        $req = MyPDO::getInstance()->prepare(<<<SQL
            SELECT * FROM Users
            WHERE email = :mail
        SQL);
        $req->execute(['mail' => $newMail]);
        if(!$req->rowCount() > 0){
            if($newMail === $repeatNewMail){
                $req2 = MyPDO::getInstance()->prepare(<<<SQL
                    UPDATE Users
                    SET email= :mail 
                    WHERE userId = :userId;
                SQL);
                $req2->execute(['userId' => $user->getUserId(), 'mail' => $newMail]);
                header('Location: ../profile.php');
            } else header('Location: ../profile_change_mail.php?err_repeatMail');
        } else header('Location: ../profile_change_mail.php?err_newMail_already_exist');
>>>>>>> 510808446538d4feacd80a6a2625e96261cf5a88
    } else header('Location: ../profile_change_mail.php?err_infos');
} else header('Location: ../connexion.php');
