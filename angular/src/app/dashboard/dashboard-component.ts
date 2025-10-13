import {Component, OnInit} from '@angular/core';
import {AuthService} from '../services/auth/auth-service';
import {Observable} from 'rxjs';
import {UserResponse} from '../models/auth/auth-response.model';

@Component({
  selector: 'app-dashboard-component',
  imports: [],
  templateUrl: './dashboard-component.html',
  styleUrls: ['./dashboard-component.css']
})
export class DashboardComponent implements OnInit{

  public user$: Observable<UserResponse | null>;
  constructor(private authService: AuthService) {
    this.user$ = this.authService.user$;
  }

  ngOnInit(): void {

  }
}
