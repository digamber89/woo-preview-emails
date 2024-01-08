<script src="<?php echo $this->plugin_url . '/assets/main.js'; ?>" type="text/javascript"></script>
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