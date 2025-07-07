<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <title>Login - E-commerce Moda y Salud</title>
</head>

<body>
  <!-- Toast notification container -->
  <div id="toast" class="fixed top-5 right-5 z-50 opacity-0 transform translate-x-full transition-all duration-300">
    <div id="toast-content" class="bg-white border-l-4 border-red-500 p-4 rounded-lg shadow-lg max-w-sm">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <i id="toast-icon" class="fas fa-exclamation-circle text-red-500"></i>
        </div>
        <div class="ml-3">
          <p id="toast-message" class="text-sm text-gray-700"></p>
        </div>
        <div class="ml-auto pl-3">
          <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <section class="bg-gray-50 min-h-screen flex items-center justify-center">


    <div class="bg-gray-100 flex rounded-2xl shadow-lg max-w-3xl p-5">
      <div class="sm:w-1/2 px-16">
        <h2 class="font-bold text-2xl text-[#FC8BA5]">Login</h2>
        <p class="text-sm mt-4 text-gray-500">
          Si ya tienes cuenta
          , inicia Sesión Facilmente
        </p>
        <form method="POST" action="index.php" class="flex flex-col gap-4" id="loginForm">
          <div class="relative">
            <input id="txtUsua" class="p-2 mt-8 rounded-xl text-xs bg-gray-200 text-gray-700 w-full pr-10 border border-gray-200 focus:border-gray-500 focus:outline-none transition-colors duration-200" type="text" name="txtUsua" placeholder="Email o Usuario" required autocomplete="username">
            <div class="absolute right-3 top-10 text-gray-400">
              <i class="fas fa-user text-xs"></i>
            </div>
          </div>

          <div class="relative">
            <input id="txtContra" class="p-2 rounded-xl text-xs bg-gray-200 text-gray-500 w-full pr-10 border border-gray-200 focus:border-gray-500 focus:outline-none transition-colors duration-200" type="password" name="txtContra" placeholder="Contraseña" required autocomplete="current-password">
            <button type="button" onclick="togglePassword()" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
              <i id="passwordIcon" class="fas fa-eye text-xs"></i>
            </button>
          </div>

          <button id="loginButton" class="bg-[#FC8BA5] rounded-xl text-white py-2 hover:scale-105 duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center" type="submit">
            <span id="loginButtonText">Login</span>
            <div id="loginSpinner" class="hidden ml-2">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
          </button>
        </form>
        <div class="mt-6 grid grid-cols-3 items-center text-gray-400">
          <hr class="border-gray-400">
          <p class="text-center text-sm">o puedes</p>
          <hr class="border-gray-400">
        </div>
        <button class="bg-white border border-gray-300 py-2 w-full rounded-xl mt-5 flex justify-center items-center text-sm hover:scale-105 duration-300 text-gray-500">
          <svg class="mr-3" width="30px" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M2 11.9556C2 8.47078 2 6.7284 2.67818 5.39739C3.27473 4.22661 4.22661 3.27473 5.39739 2.67818C6.7284 2 8.47078 2 11.9556 2H20.0444C23.5292 2 25.2716 2 26.6026 2.67818C27.7734 3.27473 28.7253 4.22661 29.3218 5.39739C30 6.7284 30 8.47078 30 11.9556V20.0444C30 23.5292 30 25.2716 29.3218 26.6026C28.7253 27.7734 27.7734 28.7253 26.6026 29.3218C25.2716 30 23.5292 30 20.0444 30H11.9556C8.47078 30 6.7284 30 5.39739 29.3218C4.22661 28.7253 3.27473 27.7734 2.67818 26.6026C2 25.2716 2 23.5292 2 20.0444V11.9556Z" fill="white"></path>
              <path d="M22.0515 8.52295L16.0644 13.1954L9.94043 8.52295V8.52421L9.94783 8.53053V15.0732L15.9954 19.8466L22.0515 15.2575V8.52295Z" fill="#EA4335"></path>
              <path d="M23.6231 7.38639L22.0508 8.52292V15.2575L26.9983 11.459V9.17074C26.9983 9.17074 26.3978 5.90258 23.6231 7.38639Z" fill="#FBBC05"></path>
              <path d="M22.0508 15.2575V23.9924H25.8428C25.8428 23.9924 26.9219 23.8813 26.9995 22.6513V11.459L22.0508 15.2575Z" fill="#34A853"></path>
              <path d="M9.94811 24.0001V15.0732L9.94043 15.0669L9.94811 24.0001Z" fill="#C5221F"></path>
              <path d="M9.94014 8.52404L8.37646 7.39382C5.60179 5.91001 5 9.17692 5 9.17692V11.4651L9.94014 15.0667V8.52404Z" fill="#C5221F"></path>
              <path d="M9.94043 8.52441V15.0671L9.94811 15.0734V8.53073L9.94043 8.52441Z" fill="#C5221F"></path>
              <path d="M5 11.4668V22.6591C5.07646 23.8904 6.15673 24.0003 6.15673 24.0003H9.94877L9.94014 15.0671L5 11.4668Z" fill="#4285F4"></path>
            </g>
          </svg>

          Solicitar una Cuenta
        </button>
        <div class="mt-5 text-xs border-b border-gray-400 py-4 text-[#392E2C]">
          <a href="#">¿Olvidaste tu contraseña?</a>
        </div>
      </div>

      <div class="sm:block hidden w-1/2">
        <img class="rounded-2xl" src="./img/login.jpg" alt="">
      </div>
    </div>
  </section>
</body>

</html>
<script>
  // Toast notification functions
  function showToast(message, type = 'error') {
    const toast = document.getElementById('toast');
    const toastContent = document.getElementById('toast-content');
    const toastMessage = document.getElementById('toast-message');
    const toastIcon = document.getElementById('toast-icon');

    toastMessage.textContent = message;

    // Reset classes
    if (type === 'success') {
      toastContent.className = 'bg-white border-l-4 border-green-500 p-4 rounded-lg shadow-lg max-w-sm';
      toastIcon.className = 'fas fa-check-circle text-green-500';
    } else if (type === 'warning') {
      toastContent.className = 'bg-white border-l-4 border-yellow-500 p-4 rounded-lg shadow-lg max-w-sm';
      toastIcon.className = 'fas fa-exclamation-triangle text-yellow-500';
    } else {
      toastContent.className = 'bg-white border-l-4 border-red-500 p-4 rounded-lg shadow-lg max-w-sm';
      toastIcon.className = 'fas fa-exclamation-circle text-red-500';
    }

    // Show toast
    setTimeout(() => {
      toast.className = 'fixed top-5 right-5 z-50 opacity-100 transform translate-x-0 transition-all duration-300';
    }, 100);

    // Auto hide after 5 seconds
    setTimeout(hideToast, 5000);
  }

  function hideToast() {
    const toast = document.getElementById('toast');
    toast.className = 'fixed top-5 right-5 z-50 opacity-0 transform translate-x-full transition-all duration-300';
  }

  // Password visibility toggle
  function togglePassword() {
    const passwordInput = document.getElementById('txtContra');
    const passwordIcon = document.getElementById('passwordIcon');

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      passwordIcon.className = 'fas fa-eye-slash text-xs';
    } else {
      passwordInput.type = 'password';
      passwordIcon.className = 'fas fa-eye text-xs';
    }
  }

  // Form validation functions
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  function validateUsername(username) {
    const re = /^[a-zA-Z0-9_]{3,}$/;
    return re.test(username);
  }

  function validateInput(input) {
    const value = input.trim();
    if (value.includes('@')) {
      return validateEmail(value);
    } else {
      return validateUsername(value);
    }
  }

  // Real-time input validation
  document.getElementById('txtUsua').addEventListener('input', function(e) {
    const input = e.target;
    const value = input.value.trim();

    if (value && !validateInput(value)) {
      input.classList.add('border-red-300', 'focus:border-red-500');
      input.classList.remove('border-gray-200', 'focus:border-gray-500');
    } else {
      input.classList.remove('border-red-300', 'focus:border-red-500');
      input.classList.add('border-gray-200', 'focus:border-gray-500');
    }
  });

  document.getElementById('txtContra').addEventListener('input', function(e) {
    const input = e.target;
    const value = input.value;

    if (value && value.length < 6) {
      input.classList.add('border-red-300', 'focus:border-red-500');
      input.classList.remove('border-gray-200', 'focus:border-gray-500');
    } else {
      input.classList.remove('border-red-300', 'focus:border-red-500');
      input.classList.add('border-gray-200', 'focus:border-gray-500');
    }
  });

  // Enhanced form submission
  document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const loginButton = document.getElementById('loginButton');
    const loginButtonText = document.getElementById('loginButtonText');
    const loginSpinner = document.getElementById('loginSpinner');
    const userInput = document.getElementById('txtUsua').value;
    const passwordInput = document.getElementById('txtContra').value;

    // Basic validation
    if (!userInput || !passwordInput) {
      showToast('Por favor, completa todos los campos', 'warning');
      return;
    }

    if (!validateInput(userInput)) {
      showToast('Por favor, ingresa un email válido o un nombre de usuario válido', 'warning');
      return;
    }

    if (passwordInput.length < 6) {
      showToast('La contraseña debe tener al menos 6 caracteres', 'warning');
      return;
    }

    // Show loading state
    loginButton.disabled = true;
    loginButtonText.textContent = 'Iniciando sesión...';
    loginSpinner.classList.remove('hidden');

    fetch("./dashboard-web/users/login.php", {
        method: "POST",
        body: formData
    })
    .then(res => {
      if (!res.ok) {
        throw new Error(`Error HTTP: ${res.status}`);
      }
      return res.text(); // First get as text to debug
    })
    .then(text => {
      try {
        return JSON.parse(text);
      } catch (e) {
        console.error('Response is not valid JSON:', text);
        throw new Error('Respuesta inválida del servidor');
      }
    })
    .then(data => {
        if (data.success) {
            showToast('¡Inicio de sesión exitoso! Redirigiendo...', 'success');
            setTimeout(() => {
              window.location.href = "dashboard-web/ventas/analisis.php";
            }, 1500);
        } else {
            showToast(data.message || 'Credenciales incorrectas. Intenta nuevamente.', 'error');
        }
    })
      .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión. Por favor, intenta nuevamente.', 'error');
      })
      .finally(() => {
        // Reset loading state
        loginButton.disabled = false;
        loginButtonText.textContent = 'Login';
        loginSpinner.classList.add('hidden');
      });
  });

  // Focus on first input when page loads
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('txtUsua').focus();
  });

  // Add keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    // Enter key submits form
    if (e.key === 'Enter' && (e.target.id === 'txtUsua' || e.target.id === 'txtContra')) {
      document.getElementById('loginForm').dispatchEvent(new Event('submit'));
    }
  });
</script>