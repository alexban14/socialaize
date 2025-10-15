import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import { useAuth } from '@/hooks/useAuth';
import api from '@/lib/api';
import { User } from '@/schemas/user';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Loader2, MapPin, Link as LinkIcon, Calendar, Edit, Settings } from 'lucide-react';
// import { InteractionButtons } from '@/components/InteractionButtons'; // Assuming this will be created

const fetchUserProfile = async (userId: string): Promise<User> => {
  const { data } = await api.get(`/users/${userId}`); // Assuming a /users/{id} endpoint
  return data;
};

export const UserProfile = () => {
  const { userId: paramUserId } = useParams<{ userId: string }>();
  const { user: authenticatedUser } = useAuth();
  const navigate = useNavigate();

  const userId = paramUserId || authenticatedUser?.id.toString();
  const isOwnProfile = !paramUserId || paramUserId === authenticatedUser?.id.toString();

  const { data: profile, isLoading, error } = useQuery<User>({
    queryKey: ['profile', userId],
    queryFn: () => fetchUserProfile(userId!),
    enabled: !!userId,
  });

  if (isLoading) {
    return (
      <div className="flex justify-center p-8">
        <Loader2 className="h-8 w-8 animate-spin" />
      </div>
    );
  }

  if (error || !profile) {
    return <p>Error loading profile.</p>;
  }

  return (
    <div className="max-w-4xl mx-auto">
      <div className="relative h-48 md:h-64 rounded-t-2xl overflow-hidden bg-gradient-to-r from-primary to-accent">
        {profile.cover_image_url && (
          <img 
            src={profile.cover_image_url} 
            alt="Cover" 
            className="w-full h-full object-cover"
          />
        )}
      </div>

      <Card className="-mt-16 relative z-10 mx-4">
        <CardContent className="pt-8">
          <div className="flex flex-col items-center sm:flex-row sm:items-end sm:space-x-6 -mt-20 sm:-mt-16">
            <Avatar className="w-32 h-32 ring-4 ring-card">
              <AvatarImage src={profile.avatar_url || ''} alt={profile.name} />
              <AvatarFallback className="text-4xl">
                {profile.name.charAt(0)}
              </AvatarFallback>
            </Avatar>
            <div className="flex-1 mt-4 sm:mt-0 text-center sm:text-left">
              <h1 className="text-2xl font-bold">{profile.name}</h1>
              <p className="text-muted-foreground">@{profile.email}</p>
            </div>
            <div className="mt-4 sm:mt-0">
              {isOwnProfile ? (
                <Button onClick={() => navigate('/settings')}>
                  <Edit className="w-4 h-4 mr-2" />
                  Edit Profile
                </Button>
              ) : (
                // <InteractionButtons userId={profile.id.toString()} />
                <p>Interaction buttons here</p>
              )}
            </div>
          </div>

          <div className="mt-6 space-y-4">
            <p className="text-center sm:text-left">{profile.bio || 'No bio yet.'}</p>
            <div className="flex flex-wrap justify-center sm:justify-start gap-4 text-sm text-muted-foreground">
              {profile.location && (
                <div className="flex items-center">
                  <MapPin className="w-4 h-4 mr-1" />
                  <span>{profile.location}</span>
                </div>
              )}
              {profile.website && (
                <div className="flex items-center">
                  <LinkIcon className="w-4 h-4 mr-1" />
                  <a href={profile.website} target="_blank" rel="noopener noreferrer" className="text-primary hover:underline">
                    {profile.website}
                  </a>
                </div>
              )}
              <div className="flex items-center">
                <Calendar className="w-4 h-4 mr-1" />
                <span>Joined {new Date(profile.created_at).toLocaleDateString()}</span>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};
