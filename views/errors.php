<?php foreach ($errors as $error) : ?>
  <div class="message">
    <span><?php echo $this->escape($error); ?></span>
    <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
  </div>
<?php endforeach; ?>