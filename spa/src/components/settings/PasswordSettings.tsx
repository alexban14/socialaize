import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useMutation } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { toast } from "sonner";
import { Loader2, KeyRound, AlertTriangle } from "lucide-react";
import { Alert, AlertDescription } from "@/components/ui/alert";
import * as authService from "@/services/authService";
import { useAuth } from "@/hooks/useAuth";

const updatePasswordSchema = z.object({
  current_password: z.string().min(1, { message: "Current password is required" }),
  password: z.string().min(8, { message: "New password must be at least 8 characters" }),
  password_confirmation: z.string(),
}).refine(data => data.password === data.password_confirmation, {
  message: "Passwords don't match",
  path: ["password_confirmation"],
});

type UpdatePasswordFormValues = z.infer<typeof updatePasswordSchema>;

export const PasswordSettings = () => {
  const { logout } = useAuth();

  const mutation = useMutation({
    mutationFn: authService.updatePassword,
    onSuccess: () => {
      toast.success("Password updated successfully!", {
        description: "You will be logged out for security reasons.",
      });
      setTimeout(() => {
        logout();
      }, 2000);
    },
    onError: (error: any) => {
      toast.error("Failed to update password", {
        description: error.response?.data?.message || error.message,
      });
    },
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<UpdatePasswordFormValues>({
    resolver: zodResolver(updatePasswordSchema),
  });

  return (
    <Card>
        <CardHeader>
            <CardTitle className="flex items-center"><KeyRound className="mr-2"/>Change Password</CardTitle>
            <CardDescription>Update your password here.</CardDescription>
        </CardHeader>
        <CardContent>
            <Alert variant="destructive" className="mb-4">
              <AlertTriangle className="h-4 w-4" />
              <AlertDescription>
                  After a successful password change, you will be logged out and asked to log in again.
              </AlertDescription>
            </Alert>
            <form onSubmit={handleSubmit(data => mutation.mutate(data))} className="space-y-4">
                <div className="space-y-2">
                    <Label htmlFor="current_password">Current Password</Label>
                    <Input id="current_password" type="password" {...register("current_password")} />
                    {errors.current_password && <p className="text-destructive text-xs mt-1">{errors.current_password.message}</p>}
                </div>
                <div className="space-y-2">
                    <Label htmlFor="password">New Password</Label>
                    <Input id="password" type="password" {...register("password")} />
                    {errors.password && <p className="text-destructive text-xs mt-1">{errors.password.message}</p>}
                </div>
                <div className="space-y-2">
                    <Label htmlFor="password_confirmation">Confirm New Password</Label>
                    <Input id="password_confirmation" type="password" {...register("password_confirmation")} />
                    {errors.password_confirmation && <p className="text-destructive text-xs mt-1">{errors.password_confirmation.message}</p>}
                </div>
                <Button type="submit" className="w-full" disabled={mutation.isPending}>
                    {mutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}Update Password
                </Button>
            </form>
        </CardContent>
    </Card>
  );
};
