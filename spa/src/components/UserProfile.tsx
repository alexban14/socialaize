import { MapPin, Link, Calendar, Edit, Plus, Settings } from "lucide-react";
import { useNavigate } from "react-router-dom";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { InteractionButtons } from "@/components/InteractionButtons";

interface UserStats {
  posts: number;
  followers: number;
  following: number;
  likes: number;
  articles: number;
  photos: number;
}

interface UserProfileProps {
  user: {
    id: string;
    name: string;
    handle: string;
    bio: string;
    avatar: string;
    coverImage: string;
    location?: string;
    website?: string;
    joinedDate: string;
    verified?: boolean;
    stats: UserStats;
  };
  isOwnProfile?: boolean;
  isFollowing?: boolean;
  onFollow?: () => void;
  onEdit?: () => void;
  onCoverUpdate?: (newCoverUrl: string) => void;
}

export function UserProfile({ 
  user, 
  isOwnProfile = false,
  isFollowing = false,
  onFollow,
  onEdit,
  onCoverUpdate
}: UserProfileProps) {
  const navigate = useNavigate();

  return (
    <div className="max-w-4xl mx-auto">
      {/* Cover Image */}
      <div className="relative h-48 md:h-64 rounded-t-2xl overflow-hidden gradient-primary">
        {user.coverImage ? (
          <img 
            src={user.coverImage} 
            alt="Cover" 
            className="w-full h-full object-cover"
          />
        ) : (
          <div className="w-full h-full gradient-primary" />
        )}
        {isOwnProfile && (
          <Button
            variant="secondary"
            size="sm"
            className="absolute top-4 right-4 bg-card/90 backdrop-blur-sm"
          >
            <Edit className="w-4 h-4 mr-2" />
            Edit Cover
          </Button>
        )}
      </div>

      {/* Profile Header */}
      <Card className="gradient-glass border-0 shadow-lg -mt-8 relative z-10">
        <CardContent className="pt-8">
          {/* Avatar Only */}
          <div className="flex justify-center -mt-20">
            <div className="relative">
              <Avatar className="w-32 h-32 ring-4 ring-card">
                <AvatarImage src={user.avatar} alt={user.name} />
                <AvatarFallback className="gradient-primary text-primary-foreground text-3xl font-bold">
                  {user.name.split(' ').map(n => n[0]).join('')}
                </AvatarFallback>
              </Avatar>
              {isOwnProfile && (
                <Button
                  variant="secondary"
                  size="sm"
                  className="absolute bottom-2 right-2 w-8 h-8 p-0 rounded-full"
                >
                  <Edit className="w-3 h-3" />
                </Button>
              )}
            </div>
          </div>

          {/* Name and Basic Info */}
          <div className="text-center mt-6 space-y-2">
            <h1 className="text-2xl md:text-3xl font-bold text-foreground">{user.name}</h1>
            <p className="text-muted-foreground text-lg">@{user.handle}</p>
          </div>

          {/* Action Buttons */}
          <div className="flex justify-center space-x-3 mt-6">
            {isOwnProfile ? (
              <>
                <Button 
                  className="gradient-primary"
                  onClick={() => navigate('/create-post')}
                >
                  <Plus className="w-4 h-4 mr-2" />
                  Create Post
                </Button>
                <Button variant="outline" size="sm" onClick={onEdit}>
                  <Edit className="w-4 h-4 mr-2" />
                  Edit Profile
                </Button>
                <Button variant="outline" size="sm" onClick={() => navigate('/settings')}>
                  <Settings className="w-4 h-4 mr-2" />
                  Settings
                </Button>
              </>
              ) : (
                <InteractionButtons userId={user.id} />
              )}
          </div>

          {/* Bio and Details */}
          <div className="mt-6 space-y-4">
            <p className="text-foreground text-lg leading-relaxed">{user.bio}</p>
            
            <div className="flex flex-wrap items-center gap-4 text-muted-foreground">
              {user.location && (
                <div className="flex items-center space-x-1">
                  <MapPin className="w-4 h-4" />
                  <span>{user.location}</span>
                </div>
              )}
              {user.website && (
                <div className="flex items-center space-x-1">
                  <Link className="w-4 h-4" />
                  <a href={user.website} className="text-primary hover:underline">
                    {user.website.replace('https://', '')}
                  </a>
                </div>
              )}
              <div className="flex items-center space-x-1">
                <Calendar className="w-4 h-4" />
                <span>Joined {user.joinedDate}</span>
              </div>
            </div>
          </div>

          {/* Stats */}
          <div className="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4">
            <div className="text-center p-3 rounded-lg bg-secondary/20">
              <div className="text-2xl font-bold text-foreground">{user.stats.posts}</div>
              <div className="text-sm text-muted-foreground">Posts</div>
            </div>
            <div className="text-center p-3 rounded-lg bg-secondary/20">
              <div className="text-2xl font-bold text-foreground">{user.stats.followers.toLocaleString()}</div>
              <div className="text-sm text-muted-foreground">Followers</div>
            </div>
            <div className="text-center p-3 rounded-lg bg-secondary/20">
              <div className="text-2xl font-bold text-foreground">{user.stats.following.toLocaleString()}</div>
              <div className="text-sm text-muted-foreground">Following</div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Content Tabs */}
      <Tabs defaultValue="posts" className="mt-6">
        <TabsList>
          <TabsTrigger value="posts">Posts</TabsTrigger>
          <TabsTrigger value="about">About</TabsTrigger>
        </TabsList>
        <TabsContent value="posts" className="mt-4">
          <p>Posts will be displayed here.</p>
        </TabsContent>
        <TabsContent value="about" className="mt-4">
          <p>More detailed about section will be here.</p>
        </TabsContent>
      </Tabs>
    </div>
  );
}
