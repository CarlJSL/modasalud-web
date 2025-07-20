<?php
$company = require(__DIR__ . '/../../../config/company_config.php');
$info = $company['company'];
$social = $info['social_media'];
?>
<footer class="w-full bg-white border-t border-gray-200 mt-12 pt-8 pb-4 text-sm text-gray-600">
  <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row md:justify-between md:items-center gap-8">
    <!-- Enlaces rápidos -->
    <div class="flex flex-col items-center md:items-start gap-2">
      <span class="font-semibold text-purple-700 mb-1"><?= htmlspecialchars($info['name']) ?></span>
      <div class="flex gap-4">
        <a href="about.php" class="hover:underline hover:text-pink-600 transition">Nosotros</a>
        <a href="contact.php" class="hover:underline hover:text-pink-600 transition">Contacto</a>
        <a href="contact.php" class="hover:underline hover:text-pink-600 transition">Ayuda</a>
      </div>
    </div>
    <!-- Información de contacto -->
    <div class="flex flex-col items-center gap-1">
      <span class="font-semibold">Contáctanos</span>
      <a href="mailto:<?= htmlspecialchars($info['email']) ?>" class="hover:underline"><?= htmlspecialchars($info['email']) ?></a>
      <a href="tel:<?= preg_replace('/[^0-9+]/', '', $info['phone']) ?>" class="hover:underline"><?= htmlspecialchars($info['phone']) ?></a>
      <?php if (!empty($info['phone_alt'])): ?>
        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $info['phone_alt']) ?>" class="hover:underline">Alternativo: <?= htmlspecialchars($info['phone_alt']) ?></a>
      <?php endif; ?>
      <span><?= htmlspecialchars($info['address']) ?>, <?= htmlspecialchars($info['city']) ?>, <?= htmlspecialchars($info['country']) ?></span>
    </div>
    <!-- Redes sociales -->
    <div class="flex flex-col items-center gap-2">
      <span class="font-semibold">Síguenos</span>
      <div class="flex gap-3">
        <?php if (!empty($social['instagram'])): ?>
          <a href="https://instagram.com/<?= ltrim($social['instagram'], '@') ?>" class="text-xl text-purple-600 hover:text-pink-500 transition" title="Instagram"><i class="fab fa-instagram"></i></a>
        <?php endif; ?>
        <?php if (!empty($social['facebook'])): ?>
          <a href="https://facebook.com/<?= ltrim($social['facebook'], '@') ?>" class="text-xl text-blue-600 hover:text-pink-500 transition" title="Facebook"><i class="fab fa-facebook"></i></a>
        <?php endif; ?>
        <?php if (!empty($social['whatsapp'])): ?>
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $social['whatsapp']) ?>" class="text-xl text-green-500 hover:text-pink-500 transition" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="mt-6 text-center text-xs text-gray-400">
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($info['name']) ?>. Todos los derechos reservados.</p>
  </div>
</footer>