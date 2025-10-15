import { useAuth } from '@/hooks/useAuth';
import { UserProfile } from '@/components/UserProfile';

const Profile = () => {
  const { user, loading } = useAuth();

  if (loading) {
    return <div>Loading...</div>;
  }

  if (!user) {
    return <div>Not authenticated</div>;
  }

  const userProfileData = {
    id: user.id.toString(),
    name: user.name,
    handle: user.email, // Using email as handle for now
    bio: user.bio || 'No bio yet.',
    avatar: user.avatar_url || '',
    coverImage: user.cover_image_url || '',
    location: user.location,
    website: user.website,
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
      <UserProfile user={userProfileData} isOwnProfile={true} />
    </main>
  );
};

export default Profile;
