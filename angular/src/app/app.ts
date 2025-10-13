import { Component, OnInit} from '@angular/core';

import {HeaderComponent} from './header/header.component';
import {RouterOutlet} from '@angular/router';
import {AuthService} from './services/auth/auth-service';


@Component({
  selector: 'app-root',
  standalone: true,
  imports: [HeaderComponent, RouterOutlet],
  templateUrl: './app.html',
  styleUrls: ['./app.css']
})
export class App implements OnInit{
  protected title = 'TY Hire';

  constructor(private authService: AuthService) {}

  ngOnInit() {
      this.authService.checkLogin();
  }
}
