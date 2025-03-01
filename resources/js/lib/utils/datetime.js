export class DateTimeRange {
  constructor(s,e){
    this.s = s
    this.e = e
  }
  isInOfRange(s, e) {
    return this.s.getTime() <= s.getTime() 
      && s.getTime() <= this.e.getTime() 
      && this.s.getTime() <= e.getTime() 
      && e.getTime() <= this.e.getTime();
  }
}

export class DateTime {
  /**
   * @param {Date} d 
  */
  static toDateList(d) {
    return [d.getFullYear(), d.getMonth(), d.getDate()]
  }
}