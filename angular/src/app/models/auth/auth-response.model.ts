export interface AuthResponse {
  success: boolean;
  user: UserResponse;
}

export interface UserResponse {
  id: number;
  email: string;
  roles: string[];
  name: string;
}
