import { useState, useEffect } from 'react';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { toast } from "sonner";
import { Loader2, Plus, Trash2, CheckCircle } from "lucide-react";
import * as userService from "@/services/userService";
import { useAuth } from '@/hooks/useAuth';
import { UserProfile } from '@/schemas/user';
import { CreateProfileDialog } from './CreateProfileDialog';
import { Badge } from "@/components/ui/badge";

export const ProfileManager = () => {
  const { user, refreshUser } = useAuth();
  const queryClient = useQueryClient();
  const [showCreateProfile, setShowCreateProfile] = useState(false);

  const { data: profiles, isLoading } = useQuery<UserProfile[]>({
    queryKey: ['profiles', user?.id],
    queryFn: userService.getProfiles,
    enabled: !!user,
  });

  const setActiveMutation = useMutation({
    mutationFn: userService.setActiveProfile,
    onSuccess: () => {
      toast.success("Active profile switched!");
      refreshUser();
      queryClient.invalidateQueries({ queryKey: ['profiles', user?.id] });
    },
    onError: (error: any) => {
      toast.error("Failed to switch profile", {
        description: error.response?.data?.message || error.message,
      });
    },
  });

  if (isLoading) {
    return <Loader2 className="animate-spin" />;
  }

  return (
    <Card>
      <CardHeader>
        <div className="flex justify-between items-center">
            <CardTitle>Manage Profiles</CardTitle>
            <Button onClick={() => setShowCreateProfile(true)} size="sm">
                <Plus className="w-4 h-4 mr-2" />
                Create Profile
            </Button>
        </div>
        <CardDescription>Manage your different user profiles.</CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        {profiles?.map(profile => (
          <div key={profile.id} className="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <p className="font-semibold">{profile.profile_type.charAt(0).toUpperCase() + profile.profile_type.slice(1)}</p>
              <p className="text-sm text-muted-foreground">{profile.title || 'No title'}</p>
            </div>
            <div className="flex items-center space-x-2">
                {profile.is_active ? (
                    <Badge><CheckCircle className="w-4 h-4 mr-2" />Active</Badge>
                ) : (
                    <Button onClick={() => setActiveMutation.mutate(profile.profile_type)} size="sm" variant="outline" disabled={setActiveMutation.isPending}>
                        Set Active
                    </Button>
                )}
              <Button size="sm" variant="destructive" disabled={profile.profile_type === 'personal'}>
                <Trash2 className="w-4 h-4" />
              </Button>
            </div>
          </div>
        ))}
      </CardContent>
      <CreateProfileDialog open={showCreateProfile} onOpenChange={setShowCreateProfile} />
    </Card>
  );
};
