import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { Loader2, User, Image as ImageIcon, Upload } from "lucide-react";
import * as authService from "@/services/authService";
import { useAuth } from "@/hooks/useAuth";
import { profileSchema, ProfileFormValues } from "@/schemas/profile";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { useRef, useState, useEffect } from "react";

export const ProfileSettings = () => {
  const { user, refreshUser } = useAuth();
  const [avatarPreview, setAvatarPreview] = useState<string | null>(user?.avatar_url || null);
  const [coverPreview, setCoverPreview] = useState<string | null>(user?.cover_image_url || null);
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [coverFile, setCoverFile] = useState<File | null>(null);
  const avatarInputRef = useRef<HTMLInputElement>(null);
  const coverInputRef = useRef<HTMLInputElement>(null);

  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
  } = useForm<ProfileFormValues>({
    resolver: zodResolver(profileSchema),
    defaultValues: {
      name: user?.name || "",
      bio: user?.bio || "",
      location: user?.location || "",
      website: user?.website || "",
    },
  });

  useEffect(() => {
    if (user) {
      setValue("name", user.name || "");
      setValue("bio", user.bio || "");
      setValue("location", user.location || "");
      setValue("website", user.website || "");
      setAvatarPreview(user.avatar || null);
      setCoverPreview(user.cover_image || null);
    }
  }, [user, setValue]);

  const profileUpdateMutation = useMutation({
    mutationFn: (formData: FormData) => authService.updateUserProfile(formData),
    onSuccess: () => {
      toast.success("Profile updated successfully!");
      refreshUser();
      setAvatarFile(null);
      setCoverFile(null);
    },
    onError: (error: any) => {
      toast.error("Failed to update profile", {
        description: error.response?.data?.message || error.message,
      });
    },
  });

  const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>, type: 'avatar' | 'cover_image') => {
    const file = event.target.files?.[0];
    if (!file) return;

    if (type === 'avatar') {
      setAvatarFile(file);
      setAvatarPreview(URL.createObjectURL(file));
    } else {
      setCoverFile(file);
      setCoverPreview(URL.createObjectURL(file));
    }
  };

  const onSubmit = (data: ProfileFormValues) => {
    const formData = new FormData();
    
    // Append text data
    formData.append('name', data.name);
    if (data.bio) formData.append('bio', data.bio);
    if (data.location) formData.append('location', data.location);
    if (data.website) formData.append('website', data.website);

    // Append files if they exist
    if (avatarFile) {
      formData.append('avatar', avatarFile);
    }
    if (coverFile) {
      formData.append('cover_image', coverFile);
    }

    profileUpdateMutation.mutate(formData);
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center"><User className="mr-2"/>Profile Information</CardTitle>
        <CardDescription>Update your public profile information.</CardDescription>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
          <div className="flex items-center space-x-4">
            <Avatar className="h-20 w-20">
              <AvatarImage src={avatarPreview || ''} />
              <AvatarFallback>{user?.name?.charAt(0)}</AvatarFallback>
            </Avatar>
            <input
              type="file"
              ref={avatarInputRef}
              className="hidden"
              accept="image/*"
              onChange={(e) => handleFileChange(e, 'avatar')}
            />
            <Button type="button" variant="outline" onClick={() => avatarInputRef.current?.click()}>
              <Upload className="mr-2 h-4 w-4" />
              Upload Avatar
            </Button>
          </div>

          <div className="space-y-2">
            <Label>Cover Image</Label>
            <div className="h-32 w-full bg-muted rounded-md flex items-center justify-center overflow-hidden">
              {coverPreview ? (
                <img src={coverPreview} className="h-full w-full object-cover rounded-md" />
              ) : (
                <ImageIcon className="h-8 w-8 text-muted-foreground" />
              )}
            </div>
             <input
              type="file"
              ref={coverInputRef}
              className="hidden"
              accept="image/*"
              onChange={(e) => handleFileChange(e, 'cover_image')}
            />
            <Button type="button" variant="outline" onClick={() => coverInputRef.current?.click()}>
              <Upload className="mr-2 h-4 w-4" />
              Upload Cover
            </Button>
          </div>

          <div className="space-y-2">
            <Label htmlFor="name">Name</Label>
            <Input id="name" {...register("name")} />
            {errors.name && <p className="text-destructive text-xs mt-1">{errors.name.message}</p>}
          </div>
          <div className="space-y-2">
            <Label htmlFor="bio">Bio</Label>
            <Textarea id="bio" {...register("bio")} />
            {errors.bio && <p className="text-destructive text-xs mt-1">{errors.bio.message}</p>}
          </div>
          <div className="space-y-2">
            <Label htmlFor="location">Location</Label>
            <Input id="location" {...register("location")} />
            {errors.location && <p className="text-destructive text-xs mt-1">{errors.location.message}</p>}
          </div>
          <div className="space-y-2">
            <Label htmlFor="website">Website</Label>
            <Input id="website" {...register("website")} />
            {errors.website && <p className="text-destructive text-xs mt-1">{errors.website.message}</p>}
          </div>
          <Button type="submit" className="w-full" disabled={profileUpdateMutation.isPending}>
            {profileUpdateMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}Save Changes
          </Button>
        </form>
      </CardContent>
    </Card>
  );
};