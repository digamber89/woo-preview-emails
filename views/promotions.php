<style>
    .cm-woo-preview-emails-promo {
        margin-top: 50px;
    }

    .cm-woo-preview-emails-promo--list {
        display: flex;
        gap: 2em;
    }

    .cm-woo-preview-emails-promo--list-item {
        max-width: 500px;
        display: flex;
        gap: 2em;
        align-items: center;
        background: #fff;
        padding: 2.5em;
        border-radius: 1em;
    }
    .cm-woo-preview-emails-promo__list-item-image img{
        width: 55px;
        height: 55px;
    }
    .cm-woo-preview-emails-promo__list-item-content h3{
        margin-top:0;
        padding: 0;
    }
</style>
<div class="cm-woo-preview-emails-promo">
    <h2>Our Plugins</h2>
    <ul class="cm-woo-preview-emails-promo--list">
        <li class="cm-woo-preview-emails-promo--list-item">
            <div class="cm-woo-preview-emails-promo__list-item-image">
                <img src="<?php echo esc_url( plugins_url( '/images/swt.png', WOO_PREVIEW_EMAILS_FILE ) ); ?>" alt="Search with Typesense"/>
            </div>
            <div class="cm-woo-preview-emails-promo__list-item-content">
                <h3>Typesense Search for WooCommerce</h3>
                <p>Create a fast search experience for your site. Give your users a search listing page or autocomplete search.</p>
                <a href="https://www.codemanas.com/downloads/typesense-search-for-woocommerce/" class="button button-primary" target="_blank" rel="noopener">
                    Learn More
                </a>
            </div>
        </li>
        <li class="cm-woo-preview-emails-promo--list-item">
            <div class="cm-woo-preview-emails-promo__list-item-image">
                <img src="<?php echo esc_url( plugins_url( '/images/cm-blocks.png', WOO_PREVIEW_EMAILS_FILE ) ); ?>" alt="Search with Typesense"/>
            </div>
            <div class="cm-woo-preview-emails-promo__list-item-content">
                <h3>CM Blocks</h3>
                <p>The hassle-free and robust WordPress plugin designed to streamline your site-building experience.</p>
                <a href="https://wordpress.org/plugins/cm-blocks/" class="button button-primary" target="_blank" rel="noopener">
                    Learn More
                </a>
            </div>
        </li>
        <li class="cm-woo-preview-emails-promo--list-item">
            <div class="cm-woo-preview-emails-promo__list-item-image">
                <img src="<?php echo esc_url( plugins_url( '/images/vcwz.png', WOO_PREVIEW_EMAILS_FILE ) ); ?>" alt="Search with Typesense"/>
            </div>
            <div class="cm-woo-preview-emails-promo__list-item-content">
                <h3>Zoom Meetings for WooCommerce</h3>
                <p>Integrate your Zoom Meetings/Webinars directly to WooCommerce or WooCommerce products and sell your Zoom meeting or webinars.</p>
                <a href="https://www.codemanas.com/downloads/zoom-meetings-for-woocommerce/" class="button button-primary" target="_blank" rel="noopener">
                    Learn More
                </a>
            </div>
        </li>
    </ul>
</div>