import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { PasswordSettings } from "@/components/settings/PasswordSettings";

const Settings = () => {
  return (
    <div className="min-h-screen bg-gradient-to-br from-background to-secondary/20 p-4">
      <div className="max-w-2xl mx-auto mt-10">
        <Tabs defaultValue="password">
          <TabsList className="grid w-full grid-cols-1">
            <TabsTrigger value="password">Password</TabsTrigger>
          </TabsList>
          <TabsContent value="password">
            <PasswordSettings />
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
};

export default Settings;
