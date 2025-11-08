import { useAuth } from '@/hooks/useAuth';
import { UserProfile as UserProfileComponent } from '@/components/UserProfile';
import { useState, useEffect } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { CreateProfileDialog } from '@/components/profile/CreateProfileDialog';

const Profile = () => {
  const { user, loading } = useAuth();
  const [activeProfileType, setActiveProfileType] = useState('personal');
  const [showCreateProfile, setShowCreateProfile] = useState(false);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (!user) {
    return <div>Not authenticated</div>;
  }

  const activeProfile = user.profiles?.find(p => p.profile_type === activeProfileType) || user.profiles?.[0];

  const userProfileData = {
    id: user.id.toString(),
    name: user.name,
    handle: user.email, // Using email as handle for now
    title: activeProfile?.title,
    bio: activeProfile?.bio || 'No bio yet.',
    avatar: user.avatar || '',
    coverImage: user.cover_image || '',
    location: activeProfile?.location,
    website: activeProfile?.website,
    skills: activeProfile?.skills || [], // Pass skills
    interests: activeProfile?.interests || [], // Pass interests
    joinedDate: new Date(user.created_at).toLocaleDateString(),
    verified: !!user.email_verified_at,
    stats: {
      posts: 0, // These would come from another API call
      followers: 0,
      following: 0,
      likes: 0,
      articles: 0,
      photos: 0,
    }
  };

  return (
    <main className="container mx-auto py-8 px-4">
        <Tabs value={activeProfileType} onValueChange={setActiveProfileType} className="w-full">
            <div className="flex justify-between items-center mb-4">
                <TabsList>
                    {user.profiles?.map(profile => (
                        <TabsTrigger key={profile.profile_type} value={profile.profile_type}>
                            {profile.profile_type.charAt(0).toUpperCase() + profile.profile_type.slice(1)}
                        </TabsTrigger>
                    ))}
                </TabsList>
                <Button onClick={() => setShowCreateProfile(true)} size="sm">
                    <Plus className="w-4 h-4 mr-2" />
                    Create Profile
                </Button>
            </div>
            {/* Pass activeProfile to UserProfileComponent */}
            <UserProfileComponent user={userProfileData} isOwnProfile={true} activeProfile={activeProfile} />
        </Tabs>
        <CreateProfileDialog open={showCreateProfile} onOpenChange={setShowCreateProfile} />
    </main>
  );
};

export default Profile;
