import { Component, OnInit } from '@angular/core';
import {ReactiveFormsModule, FormGroup, FormControl, Validators} from '@angular/forms';
import { Router } from '@angular/router';

import {AuthService} from '../../../services/auth/auth-service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  loginForm!: FormGroup;
  authFailure: boolean = false;
  authError: string = '';

  constructor(private authService : AuthService, private router: Router) {}


  ngOnInit() {
    this.loginForm = new FormGroup({
      'email' : new FormControl(null,[Validators.required, Validators.email]),
      'password' : new FormControl(null, [Validators.required, Validators.minLength(6)])
    });
  }

  onSubmit() {
    console.log(" Login Form!");
    this.authService.login(this.loginForm.get('email')?.value, this.loginForm.get('password')?.value)
      .subscribe({
        next: (response: any) => {
          if (response.success) {
            console.log('Login successful:', response.user);
            this.router.navigate(['/dashboard']);
          } else {
            this.authError = response.error.message;
          }
        },
        error: (err: any) => {
          if (err.status === 401) {
            this.authFailure = true;
          } else {
            this.authError = err.error.message;
          }
        }
    });
  }
}
