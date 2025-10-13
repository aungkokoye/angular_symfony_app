import { Injectable } from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {BehaviorSubject, catchError, Observable, of, shareReplay, tap} from 'rxjs';
import {AuthResponse, UserResponse} from '../../models/auth/auth-response.model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private apiLoginUrl = '/api/login';
  private apiMeUrl  = '/api/me'; // Adjust to your actual endpoint
  private apiLogoutUrl = '/api/logout';

  private userSubject = new BehaviorSubject<UserResponse | null>(null);
  user$ = this.userSubject.asObservable().pipe(shareReplay(1));
  constructor(private http: HttpClient) {}

  login(email: string, password: string): Observable<AuthResponse | null>  {
    return this.http.post<AuthResponse>(this.apiLoginUrl, { email, password }).pipe(
      tap(response => {
        if (response?.user) {
          this.userSubject.next(response.user); // Update the observable
        }
      }),
      catchError(() => {
        this.userSubject.next(null); // Clear on error
        return of(null);
      })
    );
  }

  checkLogin() {
    this.http.get<UserResponse>(this.apiMeUrl)
      .pipe(
        catchError(() => of(null))
      )
      .subscribe(user => this.userSubject.next(user));
  }

  logout() {
    this.http.post<UserResponse>(this.apiLogoutUrl, {})
      .pipe(
        catchError(() => of(null))
      )
      .subscribe(user => this.userSubject.next(null));
  }

  isAdmin(roles: string[] | null): boolean {
    return !!roles?.includes('ROLE_ADMIN'); // force the result to be boolean
  }

  isClient(roles: string[] | null): boolean {
    return this.isAdmin(roles) || !!roles?.includes('ROLE_CLIENT') || false;
  }

  isUser(roles: string[] | null): boolean {
    return this.isAdmin(roles) || !!roles?.includes('ROLE_USER') || false;
  }
}
