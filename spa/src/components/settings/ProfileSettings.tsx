import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { Loader2, User, Image as ImageIcon, Upload } from "lucide-react";
import * as userService from "@/services/userService";
import { useAuth } from "@/hooks/useAuth";
import { profileSchema, ProfileFormValues } from "@/schemas/profile";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { useRef, useState, useEffect } from "react";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";

export const ProfileSettings = () => {
  const { user, refreshUser } = useAuth();
  const [avatarPreview, setAvatarPreview] = useState<string | null>(
    user?.avatar || null
  );
  const [coverPreview, setCoverPreview] = useState<string | null>(
    user?.cover_image || null
  );
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [coverFile, setCoverFile] = useState<File | null>(null);
  const avatarInputRef = useRef<HTMLInputElement>(null);
  const coverInputRef = useRef<HTMLInputElement>(null);

  const form = useForm<ProfileFormValues>({
    resolver: zodResolver(profileSchema),
    defaultValues: {
      name: user?.name || "",
      title: user?.active_profile?.title || "",
      bio: user?.active_profile?.bio || "",
      location: user?.active_profile?.location || "",
      website: user?.active_profile?.website || "",
    },
  });

  useEffect(() => {
    if (user) {
      form.setValue("name", user.name || "");
      form.setValue("title", user.active_profile?.title || "");
      form.setValue("bio", user.active_profile?.bio || "");
      form.setValue("location", user.active_profile?.location || "");
      form.setValue("website", user.active_profile?.website || "");
      setAvatarPreview(user.avatar || null);
      setCoverPreview(user.cover_image || null);
    }
  }, [user, form.setValue]);

  const profileUpdateMutation = useMutation({
    mutationFn: (formData: FormData) => userService.updateUserProfile(formData),
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

  const handleFileChange = (
    event: React.ChangeEvent<HTMLInputElement>,
    type: "avatar" | "cover_image"
  ) => {
    const file = event.target.files?.[0];
    if (!file) return;

    if (type === "avatar") {
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
    formData.append("name", data.name);
    if (data.title) formData.append("title", data.title);
    if (data.bio) formData.append("bio", data.bio);
    if (data.location) formData.append("location", data.location);
    if (data.website) formData.append("website", data.website);

    // Append files if they exist
    if (avatarFile) {
      formData.append("avatar", avatarFile);
    }
    if (coverFile) {
      formData.append("cover_image", coverFile);
    }

    profileUpdateMutation.mutate(formData);
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center">
          <User className="mr-2" />
          Profile Information
        </CardTitle>
        <CardDescription>Update your public profile information.</CardDescription>
      </CardHeader>
      <CardContent>
        <Form {...form}>
          <form
            onSubmit={form.handleSubmit(onSubmit)}
            className="space-y-4"
          >
            <div className="flex items-center space-x-4">
              <Avatar className="h-20 w-20">
                <AvatarImage src={avatarPreview || ""} />
                <AvatarFallback>{user?.name?.charAt(0)}</AvatarFallback>
              </Avatar>
              <input
                type="file"
                ref={avatarInputRef}
                className="hidden"
                accept="image/*"
                onChange={(e) => handleFileChange(e, "avatar")}
              />
              <Button
                type="button"
                variant="outline"
                onClick={() => avatarInputRef.current?.click()}
              >
                <Upload className="mr-2 h-4 w-4" />
                Upload Avatar
              </Button>
            </div>

            <div className="space-y-2">
              <FormLabel>Cover Image</FormLabel>
              <div className="h-32 w-full bg-muted rounded-md flex items-center justify-center overflow-hidden">
                {coverPreview ? (
                  <img
                    src={coverPreview}
                    className="h-full w-full object-cover rounded-md"
                  />
                ) : (
                  <ImageIcon className="h-8 w-8 text-muted-foreground" />
                )}
              </div>
              <input
                type="file"
                ref={coverInputRef}
                className="hidden"
                accept="image/*"
                onChange={(e) => handleFileChange(e, "cover_image")}
              />
              <Button
                type="button"
                variant="outline"
                onClick={() => coverInputRef.current?.click()}
              >
                <Upload className="mr-2 h-4 w-4" />
                Upload Cover
              </Button>
            </div>

            <FormField
              control={form.control}
              name="name"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Name</FormLabel>
                  <FormControl>
                    <Input {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name="title"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Title</FormLabel>
                  <FormControl>
                    <Input placeholder="Your professional title" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name="bio"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Bio</FormLabel>
                  <FormControl>
                    <Textarea {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name="location"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Location</FormLabel>
                  <FormControl>
                    <Input {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name="website"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Website</FormLabel>
                  <FormControl>
                    <Input {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <Button
              type="submit"
              className="w-full"
              disabled={profileUpdateMutation.isPending}
            >
              {profileUpdateMutation.isPending && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              Save Changes
            </Button>
          </form>
        </Form>
      </CardContent>
    </Card>
  );
};