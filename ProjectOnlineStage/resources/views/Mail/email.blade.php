<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Réinitialisation du mot de passe - OFPPT</title>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Open Sans', Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
  <!-- Main Container -->
  <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
    
    <!-- Header with Gradient Background -->
    <tr>
      <td style="background: linear-gradient(135deg, #0066cc 0%, #004d99 100%); padding: 25px 0; text-align: center;">
        <img src="{{ asset('ofppt.jpg') }}" alt="Logo OFPPT" style="width: 160px; height: auto; border-radius: 4px;">
        <h1 style="color: #ffffff; margin: 15px 0 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px;">Systeme de simulation et de pilotage des ressources pour un complexe de formation professionnelle</h1>
      </td>
    </tr>

    <!-- Main Content -->
    <tr>
      <td style="padding: 40px 30px;">
        <!-- Greeting -->
        <p style="font-size: 16px; color: #4a4a4a; line-height: 1.6; margin: 0 0 25px 0;">
          Bonjour,
        </p>

        <!-- Main Message -->
        <p style="font-size: 15px; color: #4a4a4a; line-height: 1.7; margin: 0 0 25px 0;">
          Vous avez demandé la réinitialisation de votre mot de passe. Voici votre code de vérification personnel :
        </p>

        <!-- Verification Code with Click to Copy -->
        <div style="background: #f0f7ff; border-left: 4px solid #0066cc; padding: 20px; margin: 25px 0; border-radius: 0 4px 4px 0;">
          <div id="verificationCode" 
               style="font-size: 32px; font-weight: 700; color: #0066cc; text-align: center; letter-spacing: 6px; padding: 10px 0; cursor: pointer; user-select: all; position: relative;"
               onclick="copyToClipboard('{{ $username }}')"
               title="Cliquez pour copier le code">
            {{ $username }}
          </div>
          <div style="text-align: center; font-size: 12px; color: #666; margin-top: 10px;">
            <i class="fas fa-copy" style="margin-right: 5px;"></i>Cliquez sur le code pour le copier
          </div>
        </div>

        <!-- Copy Success Message (hidden by default) -->
        <div id="copyMessage" style="display: none; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; text-align: center; margin: 10px 0; font-size: 14px;">
          <i class="fas fa-check-circle" style="margin-right: 5px;"></i>Code copié dans le presse-papiers !
        </div>

        <!-- Instructions -->
        <p style="font-size: 14px; color: #666; line-height: 1.7; margin: 0 0 25px 0;">
          Pour des raisons de sécurité, ce code expirera dans <strong style="color: #e74c3c;">10 minutes</strong>.
          Ne partagez jamais ce code avec personne, y compris le personnel du support technique.
        </p>

        <!-- Action Button -->
        <div style="text-align: center; margin: 35px 0 25px 0;">
          <a href="{{ route('forgot.password.code.form') }}" 
             style="display: inline-block; background: linear-gradient(135deg, #0066cc 0%, #004d99 100%); 
                    color: #ffffff; padding: 14px 28px; text-decoration: none; 
                    border-radius: 30px; font-size: 15px; font-weight: 600; 
                    box-shadow: 0 4px 12px rgba(0, 102, 204, 0.2);
                    transition: all 0.3s ease;
                    cursor: pointer;">
            <i class="fas fa-sync-alt" style="margin-right: 8px;"></i> Utiliser mon mot de passe
          </a>
        </div>

        <!-- Help Text -->
        <p style="font-size: 13px; color: #999; line-height: 1.6; margin: 25px 0 0 0; text-align: center;">
          Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail ou 
          <a href="#" style="color: #0066cc; text-decoration: none;">nous contacter</a> 
          si vous avez des questions.
        </p>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td style="background-color: #f5f9ff; padding: 20px; text-align: center; font-size: 12px; color: #7a8ca5; border-top: 1px solid #e1e8f0;">
        <p style="margin: 0 0 10px 0;">
          <a href="#" style="color: #4a90e2; text-decoration: none; margin: 0 10px;">Aide</a>
          <span style="color: #d1d8e0;">•</span>
          <a href="#" style="color: #4a90e2; text-decoration: none; margin: 0 10px;">Contact</a>
          <span style="color: #d1d8e0;">•</span>
          <a href="#" style="color: #4a90e2; text-decoration: none; margin: 0 10px;">Confidentialité</a>
        </p>
        <p style="margin: 0; font-size: 11px; color: #aab8c2;">
          © {{ date('Y') }} OFPPT - Tous droits réservés<br>
          <span style="color: #bdc9d6;">Ceci est un message automatique, merci de ne pas y répondre.</span>
        </p>
      </td>
    </tr>
  </table>
  
  <!-- Email Client Fix for Gmail -->
  <div style="display: none; white-space: nowrap; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
  </div>
  
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <!-- JavaScript for Copy to Clipboard -->
  <script type="text/javascript">
    function copyToClipboard(text) {
      // Create a temporary textarea element
      var textarea = document.createElement('textarea');
      textarea.value = text;
      document.body.appendChild(textarea);
      
      // Select and copy the text
      textarea.select();
      document.execCommand('copy');
      
      // Remove the temporary element
      document.body.removeChild(textarea);
      
      // Show success message
      var copyMessage = document.getElementById('copyMessage');
      if (copyMessage) {
        copyMessage.style.display = 'block';
        
        // Hide the message after 3 seconds
        setTimeout(function() {
          copyMessage.style.display = 'none';
        }, 3000);
      }
      
      // Add visual feedback to the code
      var codeElement = document.getElementById('verificationCode');
      if (codeElement) {
        var originalBackground = codeElement.style.backgroundColor;
        codeElement.style.backgroundColor = '#e8f5e8';
        codeElement.style.transition = 'background-color 0.3s ease';
        
        setTimeout(function() {
          codeElement.style.backgroundColor = originalBackground;
        }, 300);
      }
    }
    
    // Fallback for modern browsers using Clipboard API
    function modernCopyToClipboard(text) {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
          showCopySuccess();
        }).catch(function() {
          // Fallback to the old method
          copyToClipboard(text);
        });
      } else {
        copyToClipboard(text);
      }
    }
    
    function showCopySuccess() {
      var copyMessage = document.getElementById('copyMessage');
      if (copyMessage) {
        copyMessage.style.display = 'block';
        setTimeout(function() {
          copyMessage.style.display = 'none';
        }, 3000);
      }
      
      var codeElement = document.getElementById('verificationCode');
      if (codeElement) {
        codeElement.style.backgroundColor = '#e8f5e8';
        codeElement.style.transition = 'background-color 0.3s ease';
        setTimeout(function() {
          codeElement.style.backgroundColor = '';
        }, 300);
      }
    }
  </script>
  
  <!-- Responsive styles -->
  <style type="text/css">
    @media only screen and (max-width: 600px) {
      .responsive-table {
        width: 100% !important;
      }
      .responsive-padding {
        padding-left: 15px !important;
        padding-right: 15px !important;
      }
    }
    
    /* Hover effects for the verification code */
    #verificationCode:hover {
      background-color: #e6f3ff !important;
      transform: scale(1.02);
      transition: all 0.2s ease;
    }
    
    /* Button hover effect */
    a[href*="verify-code"]:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 102, 204, 0.3) !important;
    }
  </style>
</body>
</html>