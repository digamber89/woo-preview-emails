<?php
/*Not the WordPress way but i'm trying to avoid any accidental CSS that would effect how the email template looks*/
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->plugin_url . '/assets/css/select2.min.css'; ?>">
<style type="text/css">
    #woocommerce-preview-email select {
        font-size: 14px;
        line-height: 2;
        color: #32373c;
        border-color: #7e8993;
        box-shadow: none;
        border-radius: 3px;
        padding: 0 24px 0 8px;
        min-height: 30px;
        max-width: 25rem;
        -webkit-appearance: none;
        background: #fff url(data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2…%205-5%202%201-7%207-7-7%202-1z%22%20fill%3D%22%23555%22%2F%3E%3C%2Fsvg%3E) no-repeat right 5px top 55%;
        background-size: 16px 16px;
        cursor: pointer;
        vertical-align: middle;
    }
    #woocommerce-preview-email .button-primary {
        background: #007cba;
        border-color: #007cba;
        color: #fff;
        text-decoration: none;
        text-shadow: none;
        display: inline-block;
        font-size: 13px;
        line-height: 2.15384615;
        min-height: 30px;
        margin: 0;
        padding: 0 10px;
        cursor: pointer;
        border-width: 1px;
        border-style: solid;
        -webkit-appearance: none;
        border-radius: 3px;
        white-space: nowrap;
        box-sizing: border-box;
    }
    #woocommerce-preview-email .regular-text {
        width: 25em;
    }
    #woocommerce-preview-email input[type=date],
    #woocommerce-preview-email input[type=datetime-local],
    #woocommerce-preview-email input[type=datetime],
    #woocommerce-preview-email input[type=email],
    #woocommerce-preview-email input[type=month],
    #woocommerce-preview-email input[type=number],
    #woocommerce-preview-email input[type=password],
    #woocommerce-preview-email input[type=search],
    #woocommerce-preview-email input[type=tel],
    #woocommerce-preview-email input[type=text],
    #woocommerce-preview-email input[type=time],
    #woocommerce-preview-email input[type=url],
    #woocommerce-preview-email input[type=week]{
        padding: 0 8px;
        line-height: 2;
        min-height: 30px;
        font-size: 14px;
        width: 25em;
    }
    #search-description {
        display: none;
    }

    #tool-options {
        width: 50%;
        background: #fff;
        border: none;
        box-shadow: 0 0 12px 1px #2c2c2c;
        position: fixed;
        top: -100%;
        left: 50%;
        transition: all 0.3s ease-in-out;
        transform:translateX(-50%);
        opacity:0;

    }

    #tool-options.active {
        top: 10%;
        opacity: 1;
    }

    #tool-wrap {
        position: relative;
        padding: 10px 20px;
    }

    #tool-wrap table select, input[type='email'] {
        width: 100%;
    }

    #show_menu {
        text-decoration: none;
        padding: 10px;
        color: #000;
        position: fixed;
        top: 10%;
        left: 5%;
        background: #fff;
        border: 2px solid;
    }
</style>