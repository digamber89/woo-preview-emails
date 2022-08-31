const { src, dest } = require('gulp')
const wpPot = require('gulp-wp-pot')

const generate_pot = () => {
  return src(['classes/**/*.php', 'views/**/*.php'])
    .pipe(wpPot({
      domain: 'woo-preview-emails',
      package: 'Preview E-mails for WooCommerce'
    }))
    .pipe(dest('languages/woo-preview-emails.pot'))
}

exports.generate_pot = generate_pot