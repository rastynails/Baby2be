function component_Ping(auto_id)
{
    this.DOMConstruct('Ping', auto_id);
    SK_Ping.init(this);
}

component_Ping.prototype =
new SK_ComponentHandler({
    construct : function( params ) {

    },

    startUpdateActivity: function( interval )
    {
        var self = this;

        SK_Ping.getInstance().addCommand('updateActivity', {
            after: function( r )
            {
                if ( !r )
                {
                    this.stop();
                    self.signIn();
                }
            }
        }).start(interval);
    },

    signIn: function()
    {
        SK_SignIn();
    }
});


/* PING */

SK_Ping = (function() {

var SK_PingCommand = function( commandName, commandObject, stack )
{
    $.extend(this, commandObject);

    this.commandName = commandName;
    this.repeatTime = false;
    this.minRepeatTime = null;

    this.stack = stack;
    this.commandTimeout = null;
    this.stopped = true;
    this.skipped = false;
    this.inProcess = false;
    this.isRootCommand = false;

    this._lastRunTime = null;
};

SK_PingCommand.PROTO = function()
{
    this._updateLastRunTime = function()
    {
        this._lastRunTime = $.now();
    };

    this._received = function( r )
    {
        this.after(r);
    },

    this._delayCommand = function()
    {
        var self = this;

        if ( this.commandTimeout )
        {
            window.clearTimeout(this.commandTimeout);
        }

        this.commandTimeout = window.setTimeout(function()
        {
            self._run();
            self.skipped = false;
        }, this.repeatTime);
    },

    this._completed = function()
    {
        this.inProcess = false;
        this._updateLastRunTime();

        if ( this.skipped || this.stopped || this.repeatTime === false )
        {
            return;
        }

        this._delayCommand();
    },

    this._getStackCommand = function()
    {
        return {
            "command": this.commandName,
            "params": this.params
        };
    };

    this._beforeStackSend = function()
    {
        if ( this.minRepeatTime === null || this.stopped || this.inProcess || this.isRootCommand )
        {
            return;
        }

        if ( $.now() - this._lastRunTime < this.minRepeatTime )
        {
            return;
        }

        this._run();
    };

    this._run = function()
    {
        if ( !this.stopped )
        {
            this.inProcess = true;
            this.stack.push(this);
        }

        if ( this.onRun )
        {
            this.onRun(this);
        }
    };

    this.params = {};
    this.before = function(){};
    this.after = function(){};

    this.start = function( repeatTime )
    {
        if ( $.isNumeric(repeatTime) )
        {
            this.repeatTime = repeatTime;
        }
        else if ( $.isPlainObject(repeatTime) )
        {
            if ( repeatTime.max )
            {
                this.repeatTime = repeatTime.max;
            }

            if ( repeatTime.min )
            {
                this.minRepeatTime = repeatTime.min == 'each' ? 0 : repeatTime.min;
            }
        }

        this.stop();
        this.stopped = false;

        if ( !this.inProcess )
        {
            this._run();
        }
    };

    this.skip = function()
    {
        this.skipped = true;
        this._delayCommand();
    };

    this.stop = function()
    {
        this.stopped = true;
    };
};

SK_PingCommand.prototype = new SK_PingCommand.PROTO();


var SK_Ping = function()
{
    var _stack = [], _commands = {};

    var _rootCommand = null;

    var beforeStackSend, sendStack, refreshRootCommand, rootOnCommandRun, genericOnCommandRun, setRootCommand;

    rootOnCommandRun = function( command )
    {
        window.setTimeout(function(){
            sendStack();
        }, 10);
    };

    genericOnCommandRun = function( command )
    {
        if ( !_rootCommand )
        {
            setRootCommand(command);
            rootOnCommandRun(command);

            return;
        }

        if ( command.repeatTime === false )
        {
            return;
        }

        if ( _rootCommand.repeatTime === false || _rootCommand.repeatTime > command.repeatTime )
        {
            setRootCommand(command);
            rootOnCommandRun(command);
        }
    };

    refreshRootCommand = function()
    {
        var rootCommand = null;

        for ( var c in _commands )
        {
            if ( _commands[c].repeatTime === false || _commands[c].stopped  )
            {
                continue;
            }

            if ( !rootCommand || _commands[c].repeatTime < rootCommand.repeatTime )
            {
                rootCommand = _commands[c];
            }
        }

        if ( rootCommand )
        {
           setRootCommand(rootCommand);
        }
    };

    setRootCommand = function( command )
    {
        if ( _rootCommand )
        {
            _rootCommand.onRun = genericOnCommandRun;
            _rootCommand.isRootCommand = false;
        }

        command.isRootCommand = true;
        _rootCommand = command;
        _rootCommand.onRun = rootOnCommandRun;
    };

    beforeStackSend = function()
    {
        for ( var c in _commands )
        {
            _commands[c]._beforeStackSend();
        }
    };

    sendStack = function()
    {
        if ( !delegate )
        {
            return;
        }

        beforeStackSend();

        if ( !_stack.length )
        {
            return;
        }

        var stack = [], commands = [];

        while ( _stack.length )
        {
            var c = _stack.pop();
            commands.push(c);

            if ( c.before() === false )
            {
                c.skip();
                continue;
            }

            stack.push(c._getStackCommand());
        }

        if ( !stack.length )
        {
            return;
        }

        var request = {
            "stack": stack
        };

        delegate.ajaxCall('ajax_Ping', {request: request}, {
            success: function(result)
            {
                if ( !result || !result.stack )
                {
                    return;
                }

                $.each(result.stack, function(i, command)
                {
                    if ( _commands[command.command] )
                    {
                        _commands[command.command]._received(command.result);
                    }
                });
            },

            complete: function()
            {
                $(commands).each(function(i, command)
                {
                    command._completed();
                });

                refreshRootCommand();
            }
        });
    };

    this.addCommand = function( commandName, commandObject )
    {
        if ( _commands[commandName] )
        {
            return _commands[commandName];
        }

        commandObject = commandObject || {};

        _commands[commandName] = new SK_PingCommand(commandName, commandObject, _stack);
        _commands[commandName].onRun = genericOnCommandRun;

        return _commands[commandName]
    };

    this.getCommand = function( commandName )
    {
        return _commands[commandName] || null;
    };
};


var pingInstance, delegate;

return {
    getInstance: function()
    {
        if ( !pingInstance )
        {
            pingInstance = new SK_Ping();
        }

        return pingInstance;
    },

    init: function( del )
    {
        delegate = del;
    }
};

})();





/* Add command example
SK.getPing().addCommand('ajaxim', {
    params: {
        p1: 1,
        p2: 3
    },
    before: function()
    {
        this.params.p1 = 4;
    },
    after: function( res )
    {

    }
}).start(5000); // repeatTime ( Number or Object {max: maxRepeatTime, min: minRepeatTime} )
*/