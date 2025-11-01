import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { LayoutGrid, LogOut, User, Search, Bell, Plus, Home, Users, Heart, MessageSquare, Briefcase, Bookmark, Kanban, CreditCard, Settings } from 'lucide-react';
import { useState } from 'react';
// import { SearchDialog } from "./SearchDialog";

export const Navbar = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [showSearch, setShowSearch] = useState(false);

  const handleLogout = () => {
    logout();
    navigate('/auth');
  };

  return (
    <header className="sticky top-0 z-30 bg-card/95 backdrop-blur-md border-b border-border shadow-sm">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link to="/" className="flex items-center space-x-3">
            <div className="w-8 h-8 gradient-primary rounded-lg flex items-center justify-center">
              <span className="text-primary-foreground font-bold text-sm">S</span>
            </div>
            <h1 className="text-xl font-bold gradient-primary bg-clip-text text-transparent">
              Socialaize
            </h1>
          </Link>

          {/* Navigation */}
          <nav className="hidden md:flex items-center space-x-1">
            <Button variant="ghost" size="sm" onClick={() => navigate('/')}>
              <Home className="w-4 h-4 mr-2" />
              Feed
            </Button>
            <Button variant="ghost" size="sm" onClick={() => navigate('/users')}>
              <Users className="w-4 h-4 mr-2" />
              People
            </Button>
            <Button variant="ghost" size="sm" onClick={() => navigate('/project-boards')}>
              <Kanban className="w-4 h-4 mr-2" />
              Projects
            </Button>
            <Button variant="ghost" size="sm" onClick={() => navigate('/jobs')}>
              <Briefcase className="w-4 h-4 mr-2" />
              Jobs
            </Button>
          </nav>

          {/* Search */}
          <div className="flex-1 max-w-md mx-8">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4" />
              <Input
                placeholder="Search posts, people, or topics..."
                className="pl-10 bg-secondary/50 border-border/50 focus:bg-card transition-colors cursor-pointer"
                readOnly
                onClick={() => setShowSearch(true)}
              />
            </div>
          </div>

          {/* User Actions */}
          <div className="flex items-center space-x-3">
            <Button variant="ghost" size="sm" onClick={() => navigate('/create-post')}>
              <Plus className="w-4 h-4" />
            </Button>
            <Button variant="ghost" size="sm" onClick={() => navigate('/notifications')}>
              <Bell className="w-4 h-4" />
            </Button>
            
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="sm" className="p-1 rounded-full">
                  <Avatar className="w-8 h-8 ring-2 ring-primary/20">
                    <AvatarImage src={user?.avatar || ''} alt={user?.name || ''} />
                    <AvatarFallback className="gradient-primary text-primary-foreground text-sm font-semibold">
                      {user?.name?.charAt(0)}
                    </AvatarFallback>
                  </Avatar>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-48">
                <DropdownMenuLabel>My Account</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={() => navigate('/profile')}>
                  <User className="w-4 h-4 mr-2" />
                  Profile
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => navigate('/bookmarks')}>
                  <Bookmark className="w-4 h-4 mr-2" />
                  Saved Posts
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => navigate('/subscriptions')}>
                  <CreditCard className="w-4 h-4 mr-2" />
                  Subscriptions
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={() => navigate('/settings')}>
                  <Settings className="w-4 h-4 mr-2" />
                  Settings
                </DropdownMenuItem>
                <DropdownMenuItem onClick={handleLogout}>
                  <LogOut className="w-4 h-4 mr-2" />
                  Log out
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
      </div>
      
      {/* <SearchDialog open={showSearch} onOpenChange={setShowSearch} /> */}
    </header>
  );
};
