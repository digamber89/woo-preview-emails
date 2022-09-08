<script src="<?php echo site_url() . '/wp-includes/js/jquery/jquery.js'; ?>" type="text/javascript"></script>
<script src="<?php echo $this->plugin_url . '/assets/select2.min.js'; ?>" type="text/javascript"></script>
<script>
  var toggleResultControls = {
    init: function () {
      this.$toolBarToggler = document.querySelector('.tool-bar-toggler')
      this.$toolBarToggler.addEventListener('click', this.toggleToolbar.bind(this))
    },
    toggleToolbar: function (e) {
      e.preventDefault();
      this.$toolBarToggler.classList.toggle('hidden')
      document.querySelector('#tool-options').classList.toggle('hidden')
    }
  }
  window.addEventListener('load', () => {
    toggleResultControls.init()
  })
</script>