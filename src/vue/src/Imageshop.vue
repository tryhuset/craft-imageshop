<template>
	<div class="elementselect">
		<input
			:name="name"
			:value="selectedString"
			type="hidden">

		<draggable v-model="selected" class="elements">
			<image-preview
				v-for="documentValue in selected"
				:document-value="documentValue"
				:key="documentValue"
				@remove="remove(documentValue)" />
		</draggable>

		<button v-if="canAddMore" class="btn add icon dashed" @click.stop.prevent="showModal">{{ buttonText }}</button>
		<div v-show="false">
			<div class="modal elementselectormodal" ref="modal">
				<div class="body has-sidebar">
					<div class="content has-sidebar">
						<div class="sidebar">
							<nav>
								<ul>
									<li>
										<a
											:class="{'sel': activeCategory == null}"
											@click.stop.prevent="activeCategory = null"
											tabindex="0"
										>
											{{ $t('ALL') }}
										</a>
									</li>
									<li v-for="category in categories" :key="category.CategoryID">
										<a
											:class="{'sel': activeCategory == category.CategoryID}"
											@click.stop.prevent="activeCategory = category.CategoryID"
											tabindex="0"
										>
											{{ category.CategoryName }}
										</a>
									</li>
								</ul>
							</nav>
						</div>
						<div class="main">
							<div class="toolbar">
								<div class="flex">
									<div class="flex-grow texticon search icon clearable">
										<input v-model="query" class="text fullwidth" type="text" autocomplete="off" :placeholder="$t('SEARCH')" ref="queryInput">
										<div v-show="query" class="clear" v-bind:title="$t('REMOVE')" @click="clearQuery"></div>
									</div>
									<div>
										<div class="btn menubtn sortmenubtn">{{ activeInterfaceNameName }}</div>
										<div class="menu">
											<ul class="padded sort-attributes">
												<li v-for="interfaceItem in interfaces">
													<a :class="{sel: activeInterfaceName == interfaceItem.Path}"
														@click="activeInterfaceName = interfaceItem.Path">
														{{ interfaceItem.Name }}
													</a>
												</li>
											</ul>
										</div>
									</div>
									<div>
										<div class="btn menubtn sortmenubtn">{{ activeLanguageName }}</div>
										<div class="menu">
											<ul class="padded sort-attributes">
												<li v-for="language in languages">
													<a :class="{sel: activeLanguage == language.key}"
														@click="activeLanguage = language.key">
														{{ $t(language.name) }}
													</a>
												</li>
											</ul>
										</div>
									</div>
									<div v-if="query && isFetchingResults" class="spinner"></div>
								</div>
							</div>
							<div class="elements" v-infinite-scroll="fetcMore">
								<ul v-if="results && results.length" class="thumbsview">
									<li v-for="result in results"
										v-if="result"
										:class="{'disabled': isDisabled(result.DocumentID), 'sel': isSelected(result.DocumentID)}"
										:key="result.DocumentID"
										tabindex="0"
										@click.stop.prevent="toggleSelect(result.DocumentID)">
										<div class="element large hasthumb">
											<div class="elementthumb">
												<img v-bind:src="result.ListThumbUrl">
											</div>
											<div class="label">{{ getName(result) }}</div>
										</div>
									</li>
								</ul>
								<div v-if="isFetchingResults" class="spinner center"></div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="footer">
					<div class="spinner hidden"></div>
					<div class="buttons right">
						<div class="btn" tabindex="0" @click.stop.prevent="hideModal">{{ $t('CANCEL') }}</div>
						<button
							:class="{'btn submit': true, 'disabled': !tempSelected.length}"
							:disabled="!tempSelected.length"
							@click="select">
							<span v-if="maxSelect == 1 || tempSelected.length === 0">{{ $t('CHOOSE') }}</span>
							<span v-else>{{ $t('CHOOSE_COUNT', {num: tempSelected.length}) }}</span>
						</button>
					</div>
<!-- 					<div class="buttons left secondary-buttons">
						<input type="file" multiple="multiple" name="assets-upload" style="display: none;">
						<div class="btn submit" data-icon="upload" style="position: relative; overflow: hidden;" role="button">Last opp filer</div>
					</div> -->
				</div>
			</div>
		</div>
	</div>
</template>

<script>

import { DOCUMENTID_INDEX } from './const.js'

import axios from 'axios'
import { debounce } from 'debounce'
import draggable from 'vuedraggable'

import ImagePreview from './components/ImagePreview.vue'

const SEPARATOR = ','

export default {
  name: 'imageshop',
  props: {
	buttonText: {
		default: 'Add image'
	},
	defaultInterfaceName: {
		type: String,
		required: true
	},
	defaultLanguage: {
		type: String,
		requried: true
	},
	maxSelect: {
		default: 0
	},
	name: {
		required: true
	},
	namespacedId: {
		required: true
	},
	initValue: {
		default: ''
	}
  },
  data() {
	return {
		activeCategory: null,
		activeInterfaceName: '',
		activeLanguage: '',
		categories: [],
		interfaces: [],
		isFetchingCategories: false,
		isFetchingInterfaces: false,
		isFetchingResults: false,
		modal: null,
		page: 0,
		pagesize: 50,
		languages: [
			{ key: 'no', name: 'NORWEGIAN' },
			{ key: 'en', name: 'ENGLISH' }
		],
		query: '',
		resultCount: 0,
		results: [],
		selected: [],
		tempSelected: [],
	}
  },
  computed: {
  	activeInterfaceNameName() {
  		// Yes it's stupid bug we're getting the interface name's name
  		const activeInterface = this.interfaces.find(interfaceItem => interfaceItem.Path === this.activeInterfaceName)
  		return activeInterface && activeInterface.Name || this.activeInterfaceName
  	},
  	activeLanguageName() {
  		const activeLanguage = this.languages.find(language => language.key === this.activeLanguage)
  		return activeLanguage && this.$t(activeLanguage.name) || this.activeLanguage
  	},
	canAddMore() {
		const maxSelect = parseInt(this.maxSelect)
		return !maxSelect || this.selected.length + this.tempSelected.length < maxSelect
	},
	selectedString() {
		return this.selected.join(SEPARATOR)
	}
  },
  methods: {
	clearQuery() {
		this.query = ''
		this.$refs.queryInput.focus()
	},
	createModal() {
		this.modal = new Garnish.Modal(this.$refs.modal)
	},
	fetch() {
		if( !this.modal ) { return }

		const results = this.fetchResults()
		const categories = this.fetchCategories()

		return Promise.all([results, categories])

	},
	fetchCategories() {
		return new Promise((resolve, reject) => {
			this.isFetchingCategories = true;
			axios.get('/actions/craft-imageshop/categories')
				.then(response => {
					response = (response
						&& response.data
						&& response.data.GetCategoriesTreeResponse
						&& response.data.GetCategoriesTreeResponse.GetCategoriesTreeResult
						&& response.data.GetCategoriesTreeResponse.GetCategoriesTreeResult.Root
						&& response.data.GetCategoriesTreeResponse.GetCategoriesTreeResult.Root.Children
						&& response.data.GetCategoriesTreeResponse.GetCategoriesTreeResult.Root.Children.CategoryTreeNode)

					this.categories = response
					resolve(response)
				})
				.catch(error => {
					reject(error)
				})
				.then(() => {
					this.isFetchingCategories = false;
				})

		})
	},
	fetchInterfaces() {
		return new Promise((resolve, reject) => {
			this.isFetchingInterfaces = true
			axios.get('/actions/craft-imageshop/interface')
				.then(response => {
					const responseData = (response
						&& response.data
						&& response.data.GetInterfacesResponse
						&& response.data.GetInterfacesResponse.GetInterfacesResult
						&& response.data.GetInterfacesResponse.GetInterfacesResult.Interface
					)

					if(responseData && Array.isArray(responseData)) {
						this.interfaces = responseData
					} else {
						this.interfaces = [responseData]
					}
				})
		})
	},
	fetcMore() {
		if( !this.modal ) { return }
		this.page++
		this.fetchResults(true)
	},
	fetchResults(append = false) {
		return new Promise((resolve, reject) => {
			this.isFetchingResults = true
			axios.get('/actions/craft-imageshop/search', {
				params: {
					categoryIds: this.activeCategory,
					interface: this.activeInterfaceName,
					language: this.activeLanguage,
					page: this.page,
					pagesize: this.pagesize,
					query: this.query
				}
			})
				.then(response => {
					if( response && response.data ) {
						const searcResult = response.data.SearchResponse.SearchResult
						const results = searcResult.DocumentList.V4Document

						if( append ) {
							// Remove all results already in array when appending
							const newResults = results && Array.isArray(results) && results.filter( result => this.results.indexOf( item => item.DocumentID === result.DocumentID ) === -1 )

							this.results = this.results.concat(newResults)
						} else {
							this.results = results
						}

						this.resultCount = searcResult.NumberOfDocuments
					}
					resolve(response)
				})
				.catch(error => {
					reject(error)
				})
				.then(() => {
					this.isFetchingResults = false
				})
		})
	},
	fetchResultsDebounce: debounce(function() {
		this.fetchResults()
	}, 300),
	getDocumentValue(documentId) {
		return `${this.activeInterfaceName}_${this.activeLanguage}_${documentId}`
	},
	getName(result) {
		const name = result.Name || false
		return name && typeof name === 'string' ? name : result.Code
	},
	getDocumentIdArray(array) {
		return array.map(item => {
			return item.split('_')[DOCUMENTID_INDEX] || false
		})
	},
	hideModal() {
		this.tempSelected = []
		this.modal && this.modal.hide()
	},
	isDisabled(documentId) {
		const tempSelectedDocumentIds = this.getDocumentIdArray(this.tempSelected)
		const selectedDocumentIds = this.getDocumentIdArray(this.selected)

		return  !tempSelectedDocumentIds.includes(documentId) && !this.canAddMore || selectedDocumentIds.includes(documentId)
	},
	isSelected(documentId) {
		return this.getDocumentIdArray(this.tempSelected).includes(documentId)
	},
	remove(documentId) {
		const selectedIndex = this.selected.findIndex(item => item === documentId)

		if( selectedIndex > -1 ) {
			this.selected.splice(selectedIndex, 1)
		}
	},
	select() {
		this.selected = this.selected.concat(this.tempSelected)
		this.hideModal()
	},
	showModal() {
		if( this.modal ) {
			this.modal.show()
		} else {
			this.createModal()
		}
	},
	toggleSelect(documentId) {
		const documentValue = this.getDocumentValue(documentId)

		console.log(documentValue)

		if( this.selected.includes(documentId) ) { return }

		const tempSelectIndex = this.tempSelected.findIndex(item => item === documentValue)

		if( tempSelectIndex > -1 ) {
			this.tempSelected.splice(tempSelectIndex, 1)
		} else if( this.canAddMore ) {
			this.tempSelected.push(documentValue)
		}
	}
  },
  mounted() {
	this.activeInterfaceName = this.defaultInterfaceName
	this.activeLanguage = this.defaultLanguage
	this.selected = this.initValue && this.initValue.split(SEPARATOR) || []
	this.fetchInterfaces()
	this.fetch()

	Craft.initUiElements()
  },
  watch: {
	activeCategory() {
		this.page = 0
		this.fetch()
	},
	activeInterfaceName() {
		this.page = 0
		this.fetch()
	},
	activeLanguage() {
		this.page = 0
		this.fetch()
	},
	modal: 'fetch',
	query() {
		this.page = 0
		this.fetchResultsDebounce()
	}
  },
  components: {
	draggable,
	ImagePreview
  }
}
</script>

<style lang="scss" scoped>
	button {
		font-size: 1em;
	}

	.center {
		display: block;
		margin: 0 auto;
	}

	.element.hasthumb .elementthumb img {
		height: auto;
		max-width: 100%;
		max-height: 100%;
	}
</style>
