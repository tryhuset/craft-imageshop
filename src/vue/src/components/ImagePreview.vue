<template>
	<div class="element large hasthumb removable">
		<a class="delete icon" :title="$t('REMOVE')" @click.stop.prevent="remove">
			<span class="visuallyhidden">{{ $t('REMOVE') }}</span>
		</a>
		<div class="elementthumb">
			<img
				v-if="imageData"
				:src="imageData.ListThumbUrl"
				:alt="imageData.Description">
		</div>
		<div class="label">
			<span v-if="imageData" class="title">{{ name }}</span>
		</div>
	</div>
</template>

<script>

import { DOCUMENTID_INDEX, DOCUMENTLANGUAGE_INDEX } from './../const.js'

import axios from 'axios'

export default {
	name: 'image-preview',
	props: {
  	documentValue: {
  		default: ''
  	}
  },
  data() {
  	return {
  		imageData: null
  	}
  },
  computed: {
  	documentArray() {
  		return this.documentValue.split('_')
  	},
  	documentId() {
  		return this.documentArray[DOCUMENTID_INDEX]
  	},
  	documentLanguage() {
  		return this.documentArray[DOCUMENTLANGUAGE_INDEX]
  	},
  	name() {
  		const name = this.imageData.Name || false
  		return name && typeof name === 'string' ? name : this.imageData.Code
  	}
  },
  methods: {
  	fetch() {
  		if( !this.documentId ) { return null }

			axios.get('/actions/craft-imageshop/search/show', {
				params: {
					documentId: this.documentId,
					language: this.documentLanguage,
				}
			})
				.then(response => {
					if( response && response.data ) {
						this.imageData = response.data.GetDocumentByIdResponse.GetDocumentByIdResult
					}
				})
				.catch(error => {
					console.log(error)
				})
  	},
  	remove() {
  		this.$emit('remove')
  	}
  },
  watch: {
  	documentId: {
  		handler: 'fetch',
  		immediate: true
  	}
  }
}
</script>
<style lang="scss" scoped>
	.element {
		cursor: move;
	}

	.visuallyhidden {
		position: absolute;
	  margin: -1px;
	  border: 0;
	  padding: 0;
	  overflow: hidden;
	  clip: rect(0 0 0 0);
	  width: 1px;
	  height: 1px;
	}
</style>
