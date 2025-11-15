import { ProfileSettings } from "@/components/settings/ProfileSettings";
import { ProfileManager } from "@/components/settings/ProfileManager";
import { Button } from "@/components/ui/button";
import { ArrowLeft } from "lucide-react";
import { useNavigate, useSearchParams } from "react-router-dom";

const EditProfile = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const profileType = searchParams.get('type');

  return (
    <div className="min-h-screen bg-gradient-to-br from-background to-secondary/20 p-4">
      <div className="max-w-2xl mx-auto mt-10 space-y-6">
        <Button variant="ghost" onClick={() => navigate('/profile')}>
          <ArrowLeft className="w-4 h-4 mr-2" />
          Back to Profile
        </Button>
        <ProfileSettings profileType={profileType || undefined} />
        <ProfileManager />
      </div>
    </div>
  );
};

export default EditProfile;
