import { ProfileSettings } from "@/components/settings/ProfileSettings";
import { ProfileManager } from "@/components/settings/ProfileManager";
import { Button } from "@/components/ui/button";
import { ArrowLeft } from "lucide-react";
import { useNavigate } from "react-router-dom";

const EditProfile = () => {
  const navigate = useNavigate();
  return (
    <div className="min-h-screen bg-gradient-to-br from-background to-secondary/20 p-4">
      <div className="max-w-2xl mx-auto mt-10 space-y-6">
        <Button variant="ghost" onClick={() => navigate('/profile')}>
          <ArrowLeft className="w-4 h-4 mr-2" />
          Back to Profile
        </Button>
        <ProfileSettings />
        <ProfileManager />
      </div>
    </div>
  );
};

export default EditProfile;
