/*!
 * Imageshop plugin for Craft CMS
 *
 * ImageshopField Field JS
 *
 * @author    Knut Svangstu
 * @copyright Copyright (c) 2018 Knut Svangstu
 * @link      https://vangenplotz.no/
 * @package   Imageshop
 * @since     2.0.0FlexTableFlexTableField
 */

import Vue from 'vue'
import VueI18n from 'vue-i18n'
import infiniteScroll from 'vue-infinite-scroll'

import Imageshop from './Imageshop.vue'

import messages from './translations/all.js'

Vue.use(VueI18n)
Vue.use(infiniteScroll)

const i18n = new VueI18n({
	locale: window.Craft.language,
	fallbackLocale: 'en',
	messages,
})

window.initVueImageshop = (id) => {
	new Vue({
		el: `#${id}`,
		components: {
			Imageshop
		},
		i18n
	})
}
