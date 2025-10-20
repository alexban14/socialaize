import { useNavigate, useSearchParams } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { z } from "zod";
import { useEffect, useState } from "react";

import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { toast } from "sonner";
import { Loader2, Mail, Lock, User, CheckCircle } from "lucide-react";
import { useAuth } from "@/hooks/useAuth";
import * as authService from "@/services/authService";
import { loginSchema, signupSchema } from "@/schemas/auth";

type LoginFormValues = z.infer<typeof loginSchema>;
type SignupFormValues = z.infer<typeof signupSchema>;

const Auth = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const { login } = useAuth();
  const [registrationSuccess, setRegistrationSuccess] = useState(false);
  const [registeredEmail, setRegisteredEmail] = useState("");

  useEffect(() => {
    const verified = searchParams.get('verified');
    const error = searchParams.get('error');

    if (verified === '1') {
      toast.success("Email verified!", {
        description: "You can now log in to your account.",
      });
    } else if (error) {
      toast.error("Verification Failed", {
        description: "The verification link is invalid or has expired.",
      });
    }
  }, [searchParams]);


  const loginMutation = useMutation({
    mutationFn: authService.login,
    onSuccess: (data) => {
      login(data.access_token);
      toast.success("Welcome back!", {
        description: "You have been successfully signed in.",
      });
      navigate("/");
    },
    onError: (error: any) => {
      toast.error("Sign in failed", {
        description: error.response?.data?.message || error.message,
      });
    },
  });

  const signupMutation = useMutation({
    mutationFn: (data: SignupFormValues) => authService.register(data),
    onSuccess: (response) => {
      setRegisteredEmail(response.user.email);
      setRegistrationSuccess(true);
      toast.success("Account created!", {
        description: "Please check your email to verify your account.",
      });
    },
    onError: (error: any) => {
      const errors = error.response?.data;
      let description = "An error occurred during sign-up.";
      if (errors) {
        description = Object.values(errors).flat().join('\n');
      }
      toast.error("Sign up failed", {
        description: description,
      });
    },
  });

  const resendMutation = useMutation({
    mutationFn: () => authService.resendVerificationEmail(),
    onSuccess: () => {
      toast.success("Verification email sent!", {
        description: "Please check your inbox for a new verification link.",
      });
    },
    onError: (error: any) => {
      toast.error("Failed to resend email", {
        description: error.response?.data?.message || "Please try again later.",
      });
    },
  });

  const {
    register: registerLogin,
    handleSubmit: handleLoginSubmit,
    formState: { errors: loginErrors },
  } = useForm<LoginFormValues>({
    resolver: zodResolver(loginSchema),
  });

  const {
    register: registerSignup,
    handleSubmit: handleSignupSubmit,
    formState: { errors: signupErrors },
  } = useForm<SignupFormValues>({
    resolver: zodResolver(signupSchema),
  });

  if (registrationSuccess) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-background to-secondary/20 p-4">
        <Card className="w-full max-w-md text-center">
          <CardHeader>
            <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
              <CheckCircle className="h-6 w-6 text-green-600" />
            </div>
            <CardTitle className="text-2xl font-bold mt-4">Registration Successful</CardTitle>
            <CardDescription>Please check your email to verify your account.</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <p className="text-muted-foreground">
              A verification link has been sent to <strong>{registeredEmail}</strong>. You need to verify your email before you can log in.
            </p>
            <Button onClick={() => navigate('/auth')} className="w-full">
              Back to Login
            </Button>
            <div className="text-sm text-muted-foreground">
              Didn't receive an email?{' '}
              <Button
                variant="link"
                className="p-0 h-auto"
                onClick={() => resendMutation.mutate()}
                disabled={resendMutation.isPending}
              >
                {resendMutation.isPending ? "Sending..." : "Resend verification link"}
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-background to-secondary/20 p-4">
      <Card className="w-full max-w-md">
        <CardHeader className="text-center">
          <CardTitle className="text-2xl font-bold">Welcome</CardTitle>
          <CardDescription>Sign in to your account or create a new one</CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs defaultValue="signin" className="w-full">
            <TabsList className="grid w-full grid-cols-2">
              <TabsTrigger value="signin">Sign In</TabsTrigger>
              <TabsTrigger value="signup">Sign Up</TabsTrigger>
            </TabsList>
            
            <TabsContent value="signin" className="pt-4">
              <form onSubmit={handleLoginSubmit((data) => loginMutation.mutate(data))} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="signin-email">Email</Label>
                  <div className="relative">
                    <Mail className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      id="signin-email"
                      type="email"
                      placeholder="Enter your email"
                      {...registerLogin("email")}
                      className="pl-10"
                    />
                    {loginErrors.email && <p className="text-destructive text-xs mt-1">{loginErrors.email.message}</p>}
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="signin-password">Password</Label>
                  <div className="relative">
                    <Lock className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      id="signin-password"
                      type="password"
                      placeholder="Enter your password"
                      {...registerLogin("password")}
                      className="pl-10"
                    />
                    {loginErrors.password && <p className="text-destructive text-xs mt-1">{loginErrors.password.message}</p>}
                  </div>
                </div>
                <Button type="submit" className="w-full" disabled={loginMutation.isPending}>
                  {loginMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                  Sign In
                </Button>
              </form>
            </TabsContent>
            
            <TabsContent value="signup" className="pt-4">
              <form onSubmit={handleSignupSubmit((data) => signupMutation.mutate(data))} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="signup-name">Name</Label>
                  <div className="relative">
                    <User className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      id="signup-name"
                      type="text"
                      placeholder="Enter your name"
                      {...registerSignup("name")}
                      className="pl-10"
                    />
                    {signupErrors.name && <p className="text-destructive text-xs mt-1">{signupErrors.name.message}</p>}
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="signup-email">Email</Label>
                  <div className="relative">
                    <Mail className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      id="signup-email"
                      type="email"
                      placeholder="Enter your email"
                      {...registerSignup("email")}
                      className="pl-10"
                    />
                    {signupErrors.email && <p className="text-destructive text-xs mt-1">{signupErrors.email.message}</p>}
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="signup-password">Password</Label>
                  <div className="relative">
                    <Lock className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      id="signup-password"
                      type="password"
                      placeholder="Create a password"
                      {...registerSignup("password")}
                      className="pl-10"
                    />
                    {signupErrors.password && <p className="text-destructive text-xs mt-1">{signupErrors.password.message}</p>}
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="signup-password-confirmation">Confirm Password</Label>
                  <div className="relative">
                    <Lock className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      id="signup-password-confirmation"
                      type="password"
                      placeholder="Confirm your password"
                      {...registerSignup("password_confirmation")}
                      className="pl-10"
                    />
                    {signupErrors.password_confirmation && <p className="text-destructive text-xs mt-1">{signupErrors.password_confirmation.message}</p>}
                  </div>
                </div>
                <Button type="submit" className="w-full" disabled={signupMutation.isPending}>
                  {signupMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                  Sign Up
                </Button>
              </form>
            </TabsContent>
          </Tabs>
        </CardContent>
      </Card>
    </div>
  );
};

export default Auth;
