const path = require('path');
const Encore = require('@symfony/webpack-encore');

const SyliusAdmin = require('@sylius-ui/admin');
const SyliusShop = require('@sylius-ui/shop');

// Admin config
const adminConfig = SyliusAdmin.getWebpackConfig(path.resolve(__dirname));

// Shop config
const shopConfig = SyliusShop.getWebpackConfig(path.resolve(__dirname));

module.exports = [shopConfig, adminConfig];
