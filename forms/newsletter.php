<?php
  /**
   * Author: Deployment Software
   * Updated: Aug 25 2025 with Bootstrap v5.3.3
   * License:
 */

  // Configuración
  $receiving_email_address = 'contact@deploymentsoftware.com'; // Cambia por tu email real

  // Verificar si existe la librería PHP Email Form
  if(file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
      include($php_email_form);
  } else {
      die('Unable to load the "PHP Email Form" Library!');
  }

  // Verificar que sea una petición POST
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      die('Method not allowed');
  }

  // Verificar que el email esté presente
  if (!isset($_POST['email']) || empty($_POST['email'])) {
      die('Email is required');
  }

  // Validar email
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      die('Invalid email format');
  }

  // Obtener IP real del usuario (considerando Cloudflare)
  $user_ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

  // Crear instancia del formulario
  $contact = new PHP_Email_Form;
  $contact->ajax = true;
  
  $contact->to = $receiving_email_address;
  $contact->from_name = 'Newsletter Subscription - ' . $_SERVER['HTTP_HOST'];
  $contact->from_email = $_POST['email'];
  $contact->subject = "New Newsletter Subscription from " . $_SERVER['HTTP_HOST'];

  // Configuración SMTP (descomenta y configura si necesitas SMTP)
  /*
  $contact->smtp = array(
      'host' => 'smtp.gmail.com',
      'username' => 'tu-email@gmail.com',
      'password' => 'tu-contraseña-app',
      'port' => '587'
  );
  */

  // Agregar información del suscriptor
  $contact->add_message($_POST['email'], 'Email');
  $contact->add_message($user_ip, 'IP Address');
  $contact->add_message(date('Y-m-d H:i:s'), 'Subscription Date');

  // Obtener información adicional de Cloudflare si está disponible
  if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
      $contact->add_message($_SERVER['HTTP_CF_IPCOUNTRY'], 'Country');
  }

  echo $contact->send();
?>
