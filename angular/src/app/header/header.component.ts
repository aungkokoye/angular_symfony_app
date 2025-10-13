import { Component} from '@angular/core';
import {Route, Router, RouterLink, RouterLinkActive} from '@angular/router';
import {AuthService} from '../services/auth/auth-service';
import {UserResponse} from '../models/auth/auth-response.model';
import {AsyncPipe} from '@angular/common';
import {Observable} from 'rxjs';

@Component({
  selector: 'app-header',
  imports: [
    RouterLink,
    RouterLinkActive,
    AsyncPipe
  ],
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent{

  public user$: Observable<UserResponse | null>;
  constructor(public authService: AuthService, private router: Router) {
    this.user$ = this.authService.user$;
  }

  onLogout() {
    this.authService.logout();
    this.router.navigate(['/']);
  }

}
