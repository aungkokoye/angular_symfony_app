import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule } from '@angular/common/http/testing';
import { AuthService } from './auth-service';

describe('AuthService', () => {
  let service: AuthService;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [AuthService]
    });
    service = TestBed.inject(AuthService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('should return true for admin role', () => {
    const roles = ['ROLE_ADMIN'];
    expect(service.isAdmin(roles)).toBe(true);
  });

  it('should return false for non-admin role', () => {
    const roles = ['ROLE_USER'];
    expect(service.isAdmin(roles)).toBe(false);
  });
});