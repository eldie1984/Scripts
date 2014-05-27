require 'httparty'
require 'ap'
require 'hirb'

class Cupi
  include HTTParty
  base_uri 'http://clubcupon.local/api'

  def initialize
    Hirb.enable
    @summary_city_fields = [:id, :name, :is_group, :is_subscribed]
  end

  def login(options={})
    options[:email] = 'matilda@mailinator.com' if options[:email].nil?
    options[:password] = 'matilda123' if options[:password].nil?
    options[:enable_debug] = '0' if options[:enable_debug].nil?

    loginParams = { :email => options[:email],
                    :password => options[:password] };

    r = self.class.post(
      "/api_users/login.json?enable_debug=#{options[:enable_debug]}&show_error=#{options[:show_error]}",
      {:body => loginParams}
    )
    @token = r["token-de-seguridad"]
    return r
  end

  def loginfb(options={})
    if options[:params].nil?
      token  = generateToken()
      options[:params] = {
        :email => "matilda_fb_#{token}@mailinator.com",
        :username => "matilda_fb_#{token}",
        :fb_user_id => generateRandom,
        :first_name => "first#{token}",
        :last_name => "last#{token}",
        :date_of_birth => "",
        :gender => "female",
        :dni => "28803540",
      }
    end
    @path = "/api_users/login_facebook.json?enable_debug=#{options[:enable_debug]}&show_error=#{options[:show_error]}"
    @response = self.class.post(@path, {:body => options[:params]})
    return @response
  end

  def randomEmail
    return "matilda_#{generateToken}@mailinator.com"
  end

  def register(options={})
    if options[:email].nil?
      options[:email] = "matilda_#{generateToken}@mailinator.com"
      options[:first_name] = "matifirst#{generateToken}"
      options[:middle_name] = ""
      options[:last_name] = "matilast#{generateToken}"
    end
    options[:password] = 'matilda123' if options[:password].nil?
    options[:dni] = '28803540'        if options[:dni].nil?
    options[:enable_debug] = '1'      if options[:enable_debug].nil?

    @options = options
    @path = "/api_users/register.json?enable_debug=#{options[:enable_debug]}&show_error=#{options[:show_error]}"

    @response = self.class.post( @path, {:body => options})
    return @response
  end


  def isLogin
    return @token.nil?
  end

  def cities(options={})
    @path = "/api_cities/cities.json?"
    unless options[:enable_debug].nil?
      @path += "enable_debug=#{options[:enable_debug]}&show_error=#{options[:show_error]}&"
    end
    unless @token.nil?
      @path += "token=#{@token}"
    end
    return self.class.get(@path)
  end

  def deals(options={})
    @path = "/api_deals/deals.json?"

    options.each do |key, value|
      @path += "#{key}=#{value}&"
    end
    return self.class.get(@path)
  end

  def buy(options={})
    @path = "/api_deals/buy.json?"
    unless @token.nil?
      @path += "token=#{@token}&"
    end
    unless options[:enable_debug].nil?
      @path += "enable_debug=#{options[:enable_debug]}"
    end

    return self.class.post(@path, {:body => options[:params]})
  end

  def view(slug, enable_debug=0)
    path = "/api_deals/view.json?slug=#{slug}&enable_debug=#{enable_debug}"
    return self.class.get(path)
  end

  def cupons(options={})
    @path = "/api_deal_users/cupons.json?"
    unless options[:enable_debug].nil?
      @path += "enable_debug=#{options[:enable_debug]}&show_error=#{options[:show_error]}&"
    end
    unless @token.nil?
      @path += "token=#{@token}"
    end
    return self.class.get(@path)
  end

  def hello(name="Guest")
    return "Hello #{name}"
  end

  def rankLatitudeAndLongitude(deals, clean = false)
    if @rank.nil? or clean
      @rank = {
        :latitude => {},
        :longitude => {},
        :count_branches => 0
      }
    end

    deals["deals"].each do |deal|
      if(deal["Deal"]["is_now"] == 1)
        @rank[:count_branches] += deal["Branches"].size
        deal["Branches"].each do |branch|
          latitude = branch["Branch"]["latitude"]
          @rank[:latitude][latitude] = increment(@rank[:latitude][latitude])
          longitude = branch["Branch"]["longitude"]
          @rank[:longitude][longitude] = increment(@rank[:longitude][longitude])
        end
      end
    end
    return @rank
  end

  def groupDealIdsByLatitudAndLogitud(deals, clean = false)
    if rank.nil? or clean
      rank = {:latitude => {}, :longitude => {}, :count_branches => 0}
    end

    deals["deals"].each do |deal|
      dealId = deal["Deal"]["id"]
      rank[:count_branches] += deal["Branches"].size
      deal["Branches"].each do |branch|
        latitude = branch["Branch"]["latitude"]
        if rank[:latitude][latitude].nil?
          rank[:latitude][latitude] = []
        end
        rank[:latitude][latitude].push dealId
        longitude = branch["Branch"]["longitude"]
        if rank[:longitude][longitude].nil?
          rank[:longitude][longitude] = []
        end
        rank[:longitude][longitude].push dealId
      end
    end
    return rank
  end

  def increment(antVal = 0)
    unless antVal.nil?
      return antVal + 1
    end
    return 1
  end

  def citySummary(options={})
    cities = cities() if options[:cities].nil?
    fields = @summary_city_fields if options[:fields].nil?
    summary = []
    cities["cities"].each do |city|
      summaryItem = {}
      fields.each do |field|
        summaryItem[field] = city['City'][field.to_s]
      end
      summary.push summaryItem
    end
    return summary
  end

  def printTable(data, options={})
    unless options[:custom_fields].nil?
      return Hirb::Helpers::Table.render extractFields(data, options[:custom_fields]) #, {:fields => options[:custom_fields]}
    else
      return Hirb::Helpers::Table.render data, options
    end
  end

  def extractFields(data, fieldsGroupedByParent)
    data.each do |item|
      compactItem = {}
      fieldsGroupedByParent.each do |parent, fields|
        fields.each do |field|
          compactItem["#{parent}.#{field}"] = item[parent][field]
        end
      end
      compactData.push compactItem
    end
    return compactData
  end

  def getIndexAndId(deals)
    i = 0
    result = []
    deals["deals"].each do |deal|
      puts "#{i}::#{deal['Deal']['id']}"
      i = i + 1
      result.push deal['Deal']['id']
    end
    result
  end

  def generateToken
    @random_token = rand(36**8).to_s(36)
  end

  def generateRandom
    @random = rand(36**8)
  end

  def compactItem item
    itemCompact = {}
    item.each {|key, value| value.each{|k2, v2| itemCompact["#{key}-#{k2}"] = v2; }}
    itemCompact
  end

  def compact items
    compactItems = []
    items.each{ |v| compactItems.push compactItem v}
    compactItems
  end

  def print items
    Hirb::Helpers::Table.render items
  end

  def get(path, query={})
    @lastParams = params
    @lastRespons =  self.class.get path, query
  end

  def post(path, params={})
      self.class.post path, params
  end
end

