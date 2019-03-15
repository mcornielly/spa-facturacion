import Vue from 'vue'

import VueRouter from 'vue-router'

Vue.use(VueRouter)


const router = new VueRouter({

	routes: [
		{path: '/', redirect: '/invoices'},
		{path: '/invoices', component: require('../components/IndexComponent.vue')},
		{path: '/invoices/create', component: require('../components/FormComponent.vue')},
		{path: '/invoices/:id/edit', component: require('../components/FormComponent.vue'), meta: {mode: 'edit'}},
		{path: '/invoices/:id', component: require('../components/ShowComponent.vue')}
	] 
})

export default router