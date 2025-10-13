import { Routes } from '@angular/router';
import {LoginComponent} from './forms/auth/login/login.component';
import {DashboardComponent} from './dashboard/dashboard-component';
import {MarketplaceComponent} from './marketplace/marketplace-component';

export const routes: Routes = [
  { path: '', component: LoginComponent, title: 'Login'},
  { path: 'dashboard', component: DashboardComponent, title: 'Dashboard'},
  { path: 'marketplace', component: MarketplaceComponent, title: 'Marketplace'},
];
