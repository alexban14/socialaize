import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Heart, Plus, X } from "lucide-react";
import { useMutation } from "@tanstack/react-query";
import * as interestService from "@/services/interestService";
import { toast } from "sonner";
import { useAuth } from "@/hooks/useAuth";
import { Interest } from "@/schemas/interest";

interface InterestsProps {
  interests: Interest[];
  isOwnProfile?: boolean;
  profileType: string;
}

export function Interests({ interests, isOwnProfile = false, profileType }: InterestsProps) {
  const { refreshUser } = useAuth();
  const [newInterestName, setNewInterestName] = useState('');

  const addMutation = useMutation({
    mutationFn: (interestName: string) => interestService.addInterestToProfile(interestName, profileType),
    onSuccess: () => {
      toast.success("Interest added!");
      refreshUser();
      setNewInterestName('');
    },
    onError: (error: any) => {
      toast.error("Failed to add interest", {
        description: error.response?.data?.message || error.message,
      });
    },
  });

  const removeMutation = useMutation({
    mutationFn: (interestId: number) => interestService.removeInterestFromProfile(interestId, profileType),
    onSuccess: () => {
      toast.success("Interest removed!");
      refreshUser();
    },
    onError: (error: any) => {
      toast.error("Failed to remove interest", {
        description: error.response?.data?.message || error.message,
      });
    },
  });

  const handleAddInterest = () => {
    if (newInterestName.trim()) {
      addMutation.mutate(newInterestName.trim());
    }
  };

  return (
    <Card className="gradient-glass">
      <CardHeader>
        <CardTitle className="flex items-center space-x-2">
          <Heart className="w-5 h-5" />
          <span>Interests</span>
          <Badge variant="secondary" className="ml-auto">
            {interests?.length || 0} interests
          </Badge>
        </CardTitle>
      </CardHeader>
      <CardContent className="space-y-6">
        {isOwnProfile && (
          <div className="space-y-3 p-4 border rounded-lg bg-secondary/20">
            <div className="flex items-center space-x-2">
              <Heart className="w-4 h-4 text-muted-foreground" />
              <span className="text-sm font-medium">Add New Interest</span>
            </div>
            <div className="flex space-x-2">
              <Input
                value={newInterestName}
                onChange={(e) => setNewInterestName(e.target.value)}
                placeholder="e.g., AI, Design, Finance"
                onKeyPress={(e) => e.key === 'Enter' && handleAddInterest()}
              />
              <Button
                onClick={handleAddInterest}
                disabled={addMutation.isPending || !newInterestName.trim()}
                size="sm"
              >
                <Plus className="w-4 h-4" />
              </Button>
            </div>
          </div>
        )}

        {interests && interests.length > 0 ? (
          <div className="flex flex-wrap gap-2">
            {interests.map((interest) => (
              <Badge key={interest.id} variant="secondary" className="flex items-center space-x-1">
                <span>{interest.name}</span>
                {isOwnProfile && (
                  <Button
                    variant="ghost"
                    size="sm"
                    className="h-4 w-4 p-0"
                    onClick={() => removeMutation.mutate(interest.id)}
                    disabled={removeMutation.isPending}
                  >
                    <X className="w-3 h-3" />
                  </Button>
                )}
              </Badge>
            ))}
          </div>
        ) : (
          <div className="text-center py-8">
            <Heart className="w-12 h-12 mx-auto mb-4 text-muted-foreground opacity-50" />
            <h3 className="text-lg font-semibold text-foreground mb-2">No interests to show</h3>
            <p className="text-muted-foreground">
              {isOwnProfile
                ? "Add your interests to get better content recommendations."
                : "This user hasn't added any interests yet."}
            </p>
          </div>
        )}
      </CardContent>
    </Card>
  );
}