import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { ProfileSettings } from "@/components/settings/ProfileSettings";
import { PasswordSettings } from "@/components/settings/PasswordSettings";

const Settings = () => {
  return (
    <div className="min-h-screen bg-gradient-to-br from-background to-secondary/20 p-4">
      <div className="max-w-2xl mx-auto mt-10">
        <Tabs defaultValue="profile">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="profile">Profile</TabsTrigger>
            <TabsTrigger value="password">Password</TabsTrigger>
          </TabsList>
          <TabsContent value="profile">
            <ProfileSettings />
          </TabsContent>
          <TabsContent value="password">
            <PasswordSettings />
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
};

export default Settings;
