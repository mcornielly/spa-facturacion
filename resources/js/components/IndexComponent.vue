<template>
	<div class="panel">
		<div class="panel-heading">
			<span class="panel-title">Facturación</span>
			<div>
				<router-link to="/invoices/create" class="btn btn-primary">
					Nueva Factura
				</router-link>
			</div>
		</div>
		<div class="panel-body">
			<table class="table table-link">
				<thead>
					<tr>
						<th>ID</th>
						<th>Fecha</th>
						<th>Número</th>
						<th>Customer</th>
						<th>Due Date</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="item in model.data" :key="item.id" @click="detailsPage(item)">
						<td class="w-1">{{ item.id }}</td>
						<td class="w-3">{{ item.date }}</td>
						<td class="w-3">{{ item.number }}</td>
						<td class="w-9">{{ item.customer.text }}</td>
						<td class="w-3">{{ item.due_date }}</td>
						<td class="w-3">{{ item.total | formatMoney }}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="panel-footer flex-between">
			<div>
				<small>Páginas {{ model.from }} - {{ model.to }} de {{ model.total }}</small>
			</div>
			<div>
				<button class="btn btn-sm" :disabled="!model.prev_page_url" @click="prevPage">
					Ant.
				</button>
				<button class="btn btn-sm" :disabled="!model.next_page_url" @click="nextPage">
					Sig.
				</button>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
import Vue from 'vue'
import { get } from '../lib/api'

	export default {
		data (){
			return {
				model: {
					data: []
				},
			}
		},

		beforeRouteEnter(to, from, next){
			get('/api/invoices', to.query)
					.then((res) => {
						next(vm => vm.setData(res))
				})

		},

		beforeRouteUpdate(to, from, next){
			get('/api/invoices', to.query)
					.then((res) => {
						this.setData(res)
						next()
				})
		},

		methods: {
			setData(res) {
				Vue.set(this.$data, 'model', res.data.invoices)
				this.page = this.model.current_page
            	
            	//Progressbar
				this.$bar.finish()				
			},	
			detailsPage(item){
				this.$router.push(`/invoices/${item.id}`)
			},
			nextPage(){
				if(this.model.next_page_url){
					const query = Object.assign({}, this.$route.query)
					query.page = query.page ? (Number(query.page) + 1) : 2

					this.$router.push({
						path: '/invoices',
						query: query
					})
				}
			},
			prevPage(){
				if(this.model.prev_page_url){
					const query = Object.assign({}, this.$route.query)
					query.page = query.page ? (Number(query.page) - 1) : 1

					this.$router.push({
						path: '/invoices',
						query: query
					})
				}
			}
		}
	}

</script>