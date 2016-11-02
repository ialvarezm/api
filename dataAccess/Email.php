<?php
    require 'PHPMailerAutoload.php';

    class Email {
        private $mail = NULL;

        public function __construct(){
            $this->mail = new PHPMailer();
		}

        public function initMailer(){
            try{
                $this->mail->isSMTP();
                $this->mail->SMTPAuth = true;
                $this->mail->Host = 'smtp.gmail.com';
                $this->mail->SMTPSecure = "ssl";
                $this->mail->Port = 465;
                $this->mail->SMTPAuth = true;
                $this->mail->Username = 'muebles.sanjose.123@gmail.com';
                $this->mail->Password = 'cosmo123';
                $this->mail->From = 'muebles.sanjose.123@gmail.com';
                $this->mail->FromName = utf8_encode("=?UTF-8?B?" . base64_encode("Muebles Rústicos San  José") .  "?=");
                $this->mail->WordWrap = 70;
            }catch (phpmailerException $e) {
                echo $e->errorMessage();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        public function sendMail($email, $body) {
            $this->initMailer();
            try{
                $this->mail->AddAddress($email);
                $this->mail->isHTML(true);
                $this->mail->Subject = utf8_encode("=?UTF-8?B?" . base64_encode("Confirmación de su orden") .  "?=");
                $this->mail->msgHTML($body);
                $this->mail->SMTPDebug = 3;
                $this->mail->send();
                $this->mail->ClearAddresses();
                $this->mail->ClearCCs();
                $this->mail->ClearBCCs();
            }catch (phpmailerException $e) {
                echo $e->errorMessage();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
?>
