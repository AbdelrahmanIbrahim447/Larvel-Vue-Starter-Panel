import Profile from './ProfileComponent.vue';
import Dashboard from './DashboardComponent.vue';
import Users from './UsersComponent.vue';

export const routes = [

		{path: '/Dashboard' , component:Dashboard},
		{path: '/Profile' , component:Profile},
		{path: '/Users' , component:Users},

	];