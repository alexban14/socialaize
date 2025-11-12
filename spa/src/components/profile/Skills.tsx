import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Brain, Plus, X } from "lucide-react";
import { useMutation } from "@tanstack/react-query";
import * as skillService from "@/services/skillService";
import { toast } from "sonner";
import { useAuth } from "@/hooks/useAuth";
import { Skill } from "@/schemas/skill";

interface SkillsProps {
  skills: Skill[];
  isOwnProfile?: boolean;
  profileType: string;
}

export function Skills({ skills, isOwnProfile = false, profileType }: SkillsProps) {
  const { refreshUser } = useAuth();
  const [newSkillName, setNewSkillName] = useState('');

  const addMutation = useMutation({
    mutationFn: (skillName: string) => skillService.addSkillToProfile(skillName, profileType),
    onSuccess: () => {
      toast.success("Skill added!");
      refreshUser();
      setNewSkillName('');
    },
    onError: (error: any) => {
      toast.error("Failed to add skill", {
        description: error.response?.data?.message || error.message,
      });
    },
  });

  const removeMutation = useMutation({
    mutationFn: (skillId: number) => skillService.removeSkillFromProfile(skillId, profileType),
    onSuccess: () => {
      toast.success("Skill removed!");
      refreshUser();
    },
    onError: (error: any) => {
      toast.error("Failed to remove skill", {
        description: error.response?.data?.message || error.message,
      });
    },
  });

  const handleAddSkill = () => {
    if (newSkillName.trim()) {
      addMutation.mutate(newSkillName.trim());
    }
  };

  return (
    <Card className="gradient-glass">
      <CardHeader>
        <CardTitle className="flex items-center space-x-2">
          <Brain className="w-5 h-5" />
          <span>Skills</span>
          <Badge variant="secondary" className="ml-auto">
            {skills?.length || 0} skills
          </Badge>
        </CardTitle>
      </CardHeader>
      <CardContent className="space-y-6">
        {isOwnProfile && (
          <div className="space-y-3 p-4 border rounded-lg bg-secondary/20">
            <div className="flex items-center space-x-2">
              <Brain className="w-4 h-4 text-muted-foreground" />
              <span className="text-sm font-medium">Add New Skill</span>
            </div>
            <div className="flex space-x-2">
              <Input
                value={newSkillName}
                onChange={(e) => setNewSkillName(e.target.value)}
                placeholder="e.g., React, Python, Data Analysis"
                onKeyPress={(e) => e.key === 'Enter' && handleAddSkill()}
              />
              <Button
                onClick={handleAddSkill}
                disabled={addMutation.isPending || !newSkillName.trim()}
                size="sm"
              >
                <Plus className="w-4 h-4" />
              </Button>
            </div>
          </div>
        )}

        {skills && skills.length > 0 ? (
          <div className="flex flex-wrap gap-2">
            {skills.map((skill) => (
              <Badge key={skill.id} variant="secondary" className="flex items-center space-x-1">
                <span>{skill.name}</span>
                {isOwnProfile && (
                  <Button
                    variant="ghost"
                    size="sm"
                    className="h-4 w-4 p-0"
                    onClick={() => removeMutation.mutate(skill.id)}
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
            <Brain className="w-12 h-12 mx-auto mb-4 text-muted-foreground opacity-50" />
            <h3 className="text-lg font-semibold text-foreground mb-2">No skills to show</h3>
            <p className="text-muted-foreground">
              {isOwnProfile
                ? "Add your skills to build your profile."
                : "This user hasn't added any skills yet."}
            </p>
          </div>
        )}
      </CardContent>
    </Card>
  );
}